<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\RfidCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        $categories = MenuItem::select('category')->distinct()->pluck('category');
        $menuItems = MenuItem::where('is_available', true)->get();
        return view('canteen.cashier.index', compact('menuItems', 'categories'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $uid = strtoupper($request->rfid_uid);
        $card = RfidCard::where('rfid_uid', $uid)->with('user')->first();

        if (!$card) {
            return response()->json(['success' => false, 'message' => 'Kartu tidak terdaftar!'], 404);
        }

        if (!$card->is_active) {
            return response()->json(['success' => false, 'message' => 'Kartu dinonaktifkan!'], 403);
        }

        $totalAmount = 0;
        $orderItems = [];

        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            if (!$menuItem->is_available) {
                return response()->json(['success' => false, 'message' => "Menu {$menuItem->name} tidak tersedia!"], 400);
            }
            
            $subtotal = $menuItem->price * $itemData['quantity'];
            $totalAmount += $subtotal;

            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $itemData['quantity'],
                'price' => $menuItem->price,
                'subtotal' => $subtotal,
            ];
        }

        if ($card->balance < $totalAmount) {
            // Kirim respon error ke ESP32 agar buzzer bunyi error
            if ($request->device_id) {
                $firebase = app(\App\Services\FirebaseService::class);
                $firebase->sendResponse($request->device_id, [
                    'status' => 'error',
                    'message' => 'Saldo Kurang'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Saldo tidak mencukupi!',
                'balance' => number_format($card->balance, 0, ',', '.'),
                'required' => number_format($totalAmount, 0, ',', '.')
            ], 400);
        }

        $result = DB::transaction(function () use ($card, $totalAmount, $orderItems) {
            // Potong saldo
            $card->decrement('balance', $totalAmount);

            // Simpan order
            $order = Order::create([
                'user_id' => $card->user_id,
                'total_amount' => $totalAmount,
                'payment_method' => 'rfid',
                'status' => 'completed',
            ]);

            foreach ($orderItems as $item) {
                $order->items()->create($item);
            }

            return [
                'success' => true,
                'message' => 'Pembayaran berhasil!',
                'order_id' => $order->id,
                'user_name' => $card->user->name,
                'new_balance' => number_format($card->balance, 0, ',', '.')
            ];
        });

        // Kirim respon ke ESP32 agar buzzer bunyi success
        if ($request->device_id) {
            $firebase = app(\App\Services\FirebaseService::class);
            $firebase->sendResponse($request->device_id, [
                'status' => 'success',
                'user_name' => $card->user->name,
                'balance' => $card->balance,
                'message' => 'Pembayaran Berhasil'
            ]);
        }

        return response()->json($result);
    }

    public function pollScan()
    {
        $firebase = app(\App\Services\FirebaseService::class);
        $scan = $firebase->get('scans/latest');

        if ($scan && ($scan['status'] ?? '') === 'pending') {
            // Tandai sebagai processing agar tidak diambil lagi oleh polling lain
            $firebase->update('scans/latest', ['status' => 'processing']);

            return response()->json([
                'success' => true,
                'uid' => $scan['rfid_uid'],
                'device_id' => $scan['device_id'] ?? 'unknown'
            ]);
        }

        return response()->json(['success' => false]);
    }
}

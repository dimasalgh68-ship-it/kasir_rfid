<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\RfidCard;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::where('is_available', true)->get();
        $categories = MenuItem::select('category')->distinct()->pluck('category');
        return view('canteen.cashier.index', compact('menuItems', 'categories'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'device_id' => 'nullable|string',
            'items' => 'required|array|min:1',
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
        $menuItemsToUpdate = [];

        foreach ($request->items as $itemData) {
            $menuItem = MenuItem::find($itemData['id']);
            
            if (!$menuItem->is_available) {
                return response()->json(['success' => false, 'message' => "Menu {$menuItem->name} tidak tersedia!"], 400);
            }

            if ($menuItem->stock < $itemData['quantity']) {
                return response()->json(['success' => false, 'message' => "Stok {$menuItem->name} tidak mencukupi! (Sisa: {$menuItem->stock})"], 400);
            }
            
            $subtotal = $menuItem->price * $itemData['quantity'];
            $totalAmount += $subtotal;

            $orderItems[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $itemData['quantity'],
                'price' => $menuItem->price,
                'subtotal' => $subtotal,
            ];

            $menuItemsToUpdate[] = [
                'id' => $menuItem->id,
                'qty' => $itemData['quantity']
            ];
        }

        try {
            $result = DB::transaction(function () use ($card, $totalAmount, $orderItems, $menuItemsToUpdate) {
                // Lock the card for update to prevent race conditions
                $lockedCard = RfidCard::where('id', $card->id)->lockForUpdate()->first();

                if ($lockedCard->balance < $totalAmount) {
                    throw new \Exception('insufficient_balance');
                }

                // Potong saldo
                $lockedCard->decrement('balance', $totalAmount);

                // Kurangi stok menu
                foreach ($menuItemsToUpdate as $item) {
                    $menu = MenuItem::where('id', $item['id'])->lockForUpdate()->first();
                    if ($menu) {
                        $menu->decrement('stock', $item['qty']);
                    }
                }

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

                return $order;
            });

            // Kirim respon ke ESP32 agar buzzer bunyi success
            if ($request->device_id) {
                $firebase = app(\App\Services\FirebaseService::class);
                $firebase->sendResponse($request->device_id, [
                    'status' => 'success',
                    'message' => 'Berhasil'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil!',
                'order_id' => $result->id,
                'user_name' => $card->user->name,
                'new_balance' => number_format($card->fresh()->balance, 0, ',', '.')
            ]);

        } catch (\Exception $e) {
            if ($e->getMessage() === 'insufficient_balance') {
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

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function pollScan()
    {
        $firebase = app(\App\Services\FirebaseService::class);
        $scan = $firebase->get('scans/latest');

        if ($scan && ($scan['status'] ?? '') === 'pending') {
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

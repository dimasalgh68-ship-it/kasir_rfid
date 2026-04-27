<?php

namespace App\Http\Controllers;

use App\Models\RfidCard;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\DeviceLog;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function index()
    {
        $cards = RfidCard::with('user')->latest()->get();
        $students = User::where('role', 'student')->doesntHave('rfidCard')->get();
        return view('admin.rfid.index', compact('cards', 'students'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'rfid_uid' => 'required|string|unique:rfid_cards,rfid_uid',
        ]);

        RfidCard::create([
            'user_id' => $request->user_id,
            'rfid_uid' => strtoupper($request->rfid_uid),
            'balance' => 0,
        ]);

        return back()->with('success', 'Kartu RFID berhasil didaftarkan!');
    }

    public function toggleStatus(RfidCard $card)
    {
        $card->update(['is_active' => !$card->is_active]);
        return back()->with('success', 'Status kartu berhasil diubah!');
    }

    public function destroy(RfidCard $card)
    {
        $card->delete();
        return back()->with('success', 'Kartu RFID berhasil dihapus!');
    }

    // ===== ESP32 API Endpoints =====

    /**
     * ESP32 scans RFID card - returns user info and balance
     */
    public function scan(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'device_id' => 'nullable|string',
        ]);

        $uid = strtoupper($request->rfid_uid);

        // Log device activity
        DeviceLog::create([
            'device_id' => $request->device_id ?? 'unknown',
            'action' => 'scan',
            'payload' => json_encode(['rfid_uid' => $uid]),
            'ip_address' => $request->ip(),
        ]);

        $card = RfidCard::where('rfid_uid', $uid)->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu tidak terdaftar',
                'buzzer' => 'error'
            ], 404);
        }

        if (!$card->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu dinonaktifkan',
                'buzzer' => 'error'
            ], 403);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Kartu terdeteksi',
            'data' => [
                'user_name' => $card->user->name,
                'balance' => $card->balance,
                'card_id' => $card->id,
            ],
            'buzzer' => 'success'
        ]);
    }

    /**
     * ESP32 processes payment from RFID card
     */
    public function pay(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'items' => 'nullable|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
            'device_id' => 'nullable|string',
        ]);

        $uid = strtoupper($request->rfid_uid);
        $card = RfidCard::where('rfid_uid', $uid)->first();

        if (!$card) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu tidak terdaftar',
                'buzzer' => 'error'
            ], 404);
        }

        if (!$card->is_active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Kartu dinonaktifkan',
                'buzzer' => 'error'
            ], 403);
        }

        if ($card->balance < $request->amount) {
            return response()->json([
                'status' => 'error',
                'message' => 'Saldo tidak mencukupi',
                'balance' => $card->balance,
                'buzzer' => 'error'
            ], 400);
        }

        // Deduct balance & create order
        $card->decrement('balance', $request->amount);

        $order = Order::create([
            'user_id' => $card->user_id,
            'total_amount' => $request->amount,
            'status' => 'completed',
            'payment_method' => 'rfid',
        ]);

        if ($request->has('items')) {
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_item_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
        }

        // Log device activity
        DeviceLog::create([
            'device_id' => $request->device_id ?? 'unknown',
            'action' => 'payment',
            'payload' => json_encode([
                'rfid_uid' => $uid,
                'amount' => $request->amount,
                'order_id' => $order->id,
            ]),
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil',
            'data' => [
                'user_name' => $card->user->name,
                'amount_paid' => $request->amount,
                'remaining_balance' => $card->balance,
                'order_id' => $order->id,
            ],
            'buzzer' => 'success'
        ]);
    }

    /**
     * ESP32 heartbeat - for device monitoring
     */
    public function heartbeat(Request $request)
    {
        DeviceLog::create([
            'device_id' => $request->device_id ?? 'unknown',
            'action' => 'heartbeat',
            'payload' => json_encode($request->all()),
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
    }
}

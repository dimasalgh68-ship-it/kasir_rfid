<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin() || Auth::user()->isCanteen()) {
            $orders = Order::with(['user', 'items.menuItem'])->latest()->paginate(20);
        } else {
            $orders = Auth::user()->orders()->with('items.menuItem')->latest()->paginate(20);
        }

        return view('orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items.menuItem']);
        return view('orders.show', compact('order'));
    }

    public function destroy(Order $order)
    {
        // Only admin or canteen can cancel
        if (!Auth::user()->isAdmin() && !Auth::user()->isCanteen()) {
            abort(403);
        }

        $rfidCard = \App\Models\RfidCard::where('user_id', $order->user_id)->first();

        \Illuminate\Support\Facades\DB::transaction(function() use ($order, $rfidCard) {
            // 1. Return balance
            if ($rfidCard) {
                $rfidCard->increment('balance', $order->total_amount);
            }

            // 2. Return stock
            foreach ($order->items as $item) {
                $menu = \App\Models\MenuItem::where('id', $item->menu_item_id)->lockForUpdate()->first();
                if ($menu) {
                    $menu->increment('stock', $item->quantity);
                }
            }

            // 3. Delete order
            $order->delete();
        });

        return back()->with('success', 'Pesanan berhasil dibatalkan. Saldo dan stok telah dikembalikan.');
    }
}

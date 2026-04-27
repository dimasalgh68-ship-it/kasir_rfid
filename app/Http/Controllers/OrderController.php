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
}

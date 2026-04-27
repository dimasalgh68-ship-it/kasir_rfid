<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\RfidCard;
use App\Models\Order;
use App\Models\MenuItem;
use App\Models\Topup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return $this->adminDashboard();
        } elseif ($user->isCanteen()) {
            return $this->canteenDashboard();
        }

        return $this->studentDashboard();
    }

    private function adminDashboard()
    {
        $totalStudents = User::where('role', 'student')->count();
        $totalCards = RfidCard::count();
        $activeCards = RfidCard::where('is_active', true)->count();
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total_amount');
        $totalRevenue = Order::sum('total_amount');
        $totalBalance = RfidCard::sum('balance');
        $recentOrders = Order::with(['user', 'items.menuItem'])->latest()->take(10)->get();
        $recentTopups = Topup::with('user')->latest()->take(10)->get();

        return view('dashboard', compact(
            'totalStudents', 'totalCards', 'activeCards', 'totalOrders',
            'todayOrders', 'todayRevenue', 'totalRevenue', 'totalBalance',
            'recentOrders', 'recentTopups'
        ));
    }

    private function canteenDashboard()
    {
        $todayOrders = Order::whereDate('created_at', today())->count();
        $todayRevenue = Order::whereDate('created_at', today())->sum('total_amount');
        $menuItems = MenuItem::all();
        $recentOrders = Order::with(['user', 'items.menuItem'])->latest()->take(10)->get();

        return view('dashboard', compact('todayOrders', 'todayRevenue', 'menuItems', 'recentOrders'));
    }

    private function studentDashboard()
    {
        $user = Auth::user();
        $card = $user->rfidCard;
        $recentOrders = $user->orders()->with('items.menuItem')->latest()->take(5)->get();
        $recentTopups = $user->topups()->latest()->take(5)->get();
        $totalSpent = $user->orders()->sum('total_amount');

        return view('dashboard', compact('card', 'recentOrders', 'recentTopups', 'totalSpent'));
    }

    public function checkNfc(Request $request)
    {
        $uid = strtoupper($request->uid);
        $card = RfidCard::where('rfid_uid', $uid)->with('user')->first();

        if (!$card) {
            return response()->json([
                'success' => false,
                'message' => 'Kartu tidak terdaftar'
            ]);
        }

        return response()->json([
            'success' => true,
            'user_name' => $card->user->name,
            'balance' => number_format($card->balance, 0, ',', '.'),
            'is_active' => $card->is_active
        ]);
    }
}

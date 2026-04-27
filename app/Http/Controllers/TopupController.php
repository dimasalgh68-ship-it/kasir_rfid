<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopupController extends Controller
{
    public function index()
    {
        if (Auth::user()->isAdmin()) {
            $topups = Topup::with(['user', 'approvedBy'])->latest()->get();
            $students = User::where('role', 'student')->has('rfidCard')->get();
            return view('admin.topup.index', compact('topups', 'students'));
        }

        $topups = Auth::user()->topups()->latest()->get();
        $card = Auth::user()->rfidCard;
        return view('student.topup.index', compact('topups', 'card'));
    }

    // Admin creates topup for student
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'method' => 'required|in:cash,transfer',
        ]);

        $topup = Topup::create([
            'user_id' => $request->user_id,
            'amount' => $request->amount,
            'status' => 'success',
            'method' => $request->method,
            'approved_by' => Auth::id(),
        ]);

        // Add balance to RFID card
        $rfidCard = RfidCard::where('user_id', $request->user_id)->first();
        if ($rfidCard) {
            $rfidCard->increment('balance', $request->amount);
        }

        return back()->with('success', 'Top-up saldo berhasil! Rp ' . number_format($request->amount, 0, ',', '.'));
    }
}

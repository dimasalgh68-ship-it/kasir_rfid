<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\RfidCard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Midtrans\Config;
use Midtrans\Snap;

class TopupController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = \App\Models\Setting::getValue('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
        Config::$isProduction = (bool) \App\Models\Setting::getValue('midtrans_is_production', env('MIDTRANS_IS_PRODUCTION', false));
        Config::$isSanitized = env('MIDTRANS_IS_SANITIZED', true);
        Config::$is3ds = env('MIDTRANS_IS_3DS', true);
    }

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

    public function store(Request $request){
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1000',
            'method' => 'required|in:cash,transfer',
        ]);

        // Security Check: Only admin/canteen can do cash top-ups
        if ($request->method === 'cash' && !in_array(Auth::user()->role, ['admin', 'canteen'])) {
            abort(403, 'Anda tidak memiliki akses untuk top-up tunai.');
        }

        // Security Check: Students can only top up their own account
        if (Auth::user()->role === 'student' && $request->user_id != Auth::id()) {
            abort(403, 'Anda hanya dapat melakukan top-up untuk akun sendiri.');
        }

        $rfidCard = RfidCard::where('user_id', $request->user_id)->first();
        if (!$rfidCard) {
            return back()->withErrors(['user_id' => 'Siswa ini belum memiliki kartu RFID yang terdaftar.']);
        }

        // Jika Cash, langsung sukses
        if ($request->method === 'cash') {
            DB::transaction(function() use ($request, $rfidCard) {
                Topup::create([
                    'user_id' => $request->user_id,
                    'amount' => $request->amount,
                    'status' => 'success',
                    'method' => 'cash',
                    'approved_by' => Auth::id(),
                ]);
                $rfidCard->increment('balance', $request->amount);
            });
            return back()->with('success', 'Top-up saldo berhasil!');
        }

        // Jika Transfer, gunakan Midtrans
        $referenceId = 'TOPUP-' . time() . '-' . rand(100, 999);
        
        $params = [
            'transaction_details' => [
                'order_id' => $referenceId,
                'gross_amount' => (int)$request->amount,
            ],
            'customer_details' => [
                'first_name' => User::find($request->user_id)->name,
                'email' => User::find($request->user_id)->email,
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            
            $topup = Topup::create([
                'user_id' => $request->user_id,
                'amount' => $request->amount,
                'status' => 'pending',
                'method' => 'transfer',
                'reference_id' => $referenceId,
                'snap_token' => $snapToken,
            ]);

            return back()->with([
                'success' => 'Silakan selesaikan pembayaran.',
                'snap_token' => $snapToken,
                'topup_id' => $topup->id
            ]);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghubungkan ke Midtrans: ' . $e->getMessage()]);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = \App\Models\Setting::getValue('midtrans_server_key', env('MIDTRANS_SERVER_KEY'));
        $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
        
        if ($hashed == $request->signature_key) {
            if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                $topup = Topup::where('reference_id', $request->order_id)->first();
                
                if ($topup && $topup->status !== 'success') {
                    DB::transaction(function() use ($topup) {
                        $topup->update(['status' => 'success']);
                        $rfidCard = RfidCard::where('user_id', $topup->user_id)->first();
                        if ($rfidCard) {
                            $rfidCard->increment('balance', $topup->amount);
                        }
                    });
                }
            }
        }
    }

    public function destroy(Topup $topup)
    {
        $rfidCard = RfidCard::where('user_id', $topup->user_id)->first();

        DB::transaction(function() use ($topup, $rfidCard) {
            if ($rfidCard && $topup->status === 'success') {
                $rfidCard->decrement('balance', $topup->amount);
            }
            $topup->delete();
        });

        return back()->with('success', 'Top-up berhasil dibatalkan.');
    }
}

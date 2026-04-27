@extends('layouts.app')
@section('page-title', 'Riwayat Top-Up')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Riwayat Top-Up Saldo</h1>
        <p style="color:#64748b;font-size:0.875rem;">Riwayat pengisian saldo kartu RFID Anda</p>
    </div>
    <button onclick="document.getElementById('modal-topup-student').classList.add('active')" class="btn btn-primary">
        <i data-lucide="plus-circle" size="18"></i> Top-Up Saldo
    </button>
</div>

@if($card)
<div class="balance-card" style="margin-bottom:1.5rem;max-width:400px;">
    <p style="font-size:0.8rem;opacity:0.8;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Saldo Saat Ini</p>
    <h2 style="font-size:2rem;font-weight:800;margin-top:0.25rem;">Rp {{ number_format($card->balance, 0, ',', '.') }}</h2>
    <p style="font-size:0.75rem;opacity:0.7;margin-top:0.75rem;font-family:monospace;">UID: {{ $card->rfid_uid }}</p>
</div>
@endif

<div class="table-container">
    <table class="table">
        <thead><tr><th>Jumlah</th><th>Metode</th><th>Status</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($topups as $topup)
        <tr>
            <td style="font-weight:700;color:#16a34a;">+Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-info" style="text-transform:capitalize;">{{ $topup->method }}</span></td>
            <td><span class="badge badge-{{ $topup->status === 'success' ? 'success' : 'warning' }}">{{ ucfirst($topup->status) }}</span></td>
            <td style="color:#64748b;">{{ $topup->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="4" class="empty-state">Belum ada riwayat top-up. Hubungi admin untuk isi saldo.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Top-Up Student -->
<div class="modal-overlay" id="modal-topup-student">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;">Isi Saldo Mandiri</h3>
            <button onclick="document.getElementById('modal-topup-student').classList.remove('active')" style="background:none;border:none;cursor:pointer;color:#94a3b8;"><i data-lucide="x" size="20"></i></button>
        </div>
        <form method="POST" action="{{ route('topup.store') }}">
            @csrf
            <input type="hidden" name="user_id" value="{{ Auth::id() }}">
            <input type="hidden" name="method" value="transfer">
            
            <div class="form-group">
                <label class="form-label">Jumlah Top-Up (Rp)</label>
                <input type="number" name="amount" class="form-input" placeholder="Minimal Rp 10.000" min="10000" required>
                <small style="color:#64748b;">Pembayaran akan diproses via Midtrans (Virtual Account, E-Wallet, dll)</small>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">Bayar Sekarang</button>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();
    @if(session('snap_token'))
        window.snap.pay('{{ session('snap_token') }}', {
            onSuccess: function(result){ window.location.reload(); },
            onPending: function(result){ window.location.reload(); },
            onError: function(result){ alert("Pembayaran gagal!"); },
            onClose: function(){ alert('Anda menutup popup tanpa menyelesaikan pembayaran.'); }
        });
    @endif
</script>
@endsection

@extends('layouts.app')
@section('page-title', 'Riwayat Top-Up')
@section('content')

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Riwayat Top-Up Saldo</h1>
    <p style="color:#64748b;font-size:0.875rem;">Riwayat pengisian saldo kartu RFID Anda</p>
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

<script>lucide.createIcons();</script>
@endsection

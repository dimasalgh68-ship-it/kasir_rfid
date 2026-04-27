@extends('layouts.app')
@section('page-title', 'Riwayat Order')
@section('content')

<div style="margin-bottom:1.5rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Riwayat Order</h1>
    <p style="color:#64748b;font-size:0.875rem;">Semua riwayat transaksi pembelian</p>
</div>

<div class="table-container">
    <table class="table">
        <thead><tr><th>ID</th>@if(Auth::user()->isAdmin() || Auth::user()->isCanteen())<th>Siswa</th>@endif<th>Item</th><th>Total</th><th>Metode</th><th>Status</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($orders as $order)
        <tr>
            <td style="font-weight:700;">#{{ $order->id }}</td>
            @if(Auth::user()->isAdmin() || Auth::user()->isCanteen())
            <td>{{ $order->user->name }}</td>
            @endif
            <td style="font-size:0.8rem;">
                @foreach($order->items as $item)
                    {{ $item->menuItem->name ?? '-' }} (x{{ $item->quantity }}){{ !$loop->last ? ', ' : '' }}
                @endforeach
            </td>
            <td style="font-weight:700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-info" style="text-transform:uppercase;">{{ $order->payment_method }}</span></td>
            <td><span class="badge badge-{{ $order->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($order->status) }}</span></td>
            <td style="color:#64748b;">{{ $order->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="empty-state"><i data-lucide="receipt" size="40"></i><p>Belum ada riwayat order</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@if($orders->hasPages())
<div style="margin-top:1.5rem;display:flex;justify-content:center;">
    {{ $orders->links() }}
</div>
@endif

<script>lucide.createIcons();</script>
@endsection

@extends('layouts.app')
@section('page-title', 'Dashboard')
@section('content')

<style>
    @media (max-width: 768px) {
        .balance-card h2 {
            font-size: 1.75rem !important;
            word-break: break-word;
        }
        .mobile-flex-wrap {
            flex-wrap: wrap;
        }
        .stat-card {
            padding: 1rem;
        }
    }
</style>

@if(Auth::user()->isAdmin())
{{-- ADMIN DASHBOARD --}}
<div class="grid grid-4" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;"><i data-lucide="users" size="22"></i></div>
        <div><div class="stat-label">Total Siswa</div><div class="stat-value">{{ $totalStudents }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i data-lucide="credit-card" size="22"></i></div>
        <div><div class="stat-label">Kartu Aktif</div><div class="stat-value">{{ $activeCards }}/{{ $totalCards }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i data-lucide="shopping-bag" size="22"></i></div>
        <div><div class="stat-label">Order Hari Ini</div><div class="stat-value">{{ $todayOrders }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fee2e2; color: #ef4444;"><i data-lucide="trending-up" size="22"></i></div>
        <div><div class="stat-label">Pendapatan Hari Ini</div><div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div></div>
    </div>
</div>

<div class="grid grid-2" style="margin-bottom: 1.5rem;">
    <div class="balance-card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div>
                <p style="font-size: 0.85rem; opacity: 0.8; font-weight: 600;">Total Saldo Beredar</p>
                <h2 style="font-size: 2.25rem; font-weight: 800; margin-top: 0.5rem;">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h2>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;"><i data-lucide="wallet" size="24" color="white"></i></div>
        </div>
        <p style="font-size: 0.8rem; opacity: 0.7;">Total Revenue: Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
    </div>
    <div class="card-premium">
        <h3 style="font-weight: 700; margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem;"><i data-lucide="activity" size="20" style="color: #6366f1;"></i> Top-Up Terakhir</h3>
        @forelse($recentTopups->take(4) as $topup)
        <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid #f1f5f9;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0e7ff; display: flex; align-items: center; justify-content: center;"><i data-lucide="arrow-up-circle" size="16" style="color: #6366f1;"></i></div>
                <span style="font-weight: 600; font-size: 0.85rem;">{{ $topup->user->name }}</span>
            </div>
            <span style="font-weight: 700; color: #16a34a; font-size: 0.85rem;">+Rp {{ number_format($topup->amount, 0, ',', '.') }}</span>
        </div>
        @empty
        <p style="color: #94a3b8; font-size: 0.85rem;">Belum ada top-up.</p>
        @endforelse
    </div>
</div>

{{-- Recent Orders Table --}}
<div class="table-container">
    <div style="padding: 1.25rem; border-bottom: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-weight: 700;">Transaksi Terbaru</h3>
        <a href="{{ route('orders.index') }}" class="btn btn-outline" style="font-size: 0.8rem;">Lihat Semua</a>
    </div>
    <table class="table">
        <thead><tr><th>Order ID</th><th>Siswa</th><th>Total</th><th>Metode</th><th>Status</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($recentOrders as $order)
        <tr>
            <td style="font-weight: 700;">#{{ $order->id }}</td>
            <td>{{ $order->user->name }}</td>
            <td style="font-weight: 700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-info" style="text-transform: uppercase;">{{ $order->payment_method }}</span></td>
            <td><span class="badge badge-success">{{ ucfirst($order->status) }}</span></td>
            <td style="color: #64748b;">{{ $order->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state"><i data-lucide="inbox" size="40"></i><p>Belum ada transaksi</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@elseif(Auth::user()->isCanteen())
{{-- CANTEEN DASHBOARD --}}
<div class="grid grid-3" style="margin-bottom: 1.5rem;">
    <div class="stat-card">
        <div class="stat-icon" style="background: #e0e7ff; color: #6366f1;"><i data-lucide="shopping-bag" size="22"></i></div>
        <div><div class="stat-label">Order Hari Ini</div><div class="stat-value">{{ $todayOrders }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i data-lucide="banknote" size="22"></i></div>
        <div><div class="stat-label">Pendapatan Hari Ini</div><div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i data-lucide="utensils" size="22"></i></div>
        <div><div class="stat-label">Total Menu</div><div class="stat-value">{{ $menuItems->count() }}</div></div>
    </div>
</div>

<div class="table-container">
    <div style="padding: 1.25rem; border-bottom: 1px solid #e2e8f0;"><h3 style="font-weight: 700;">Pesanan Terbaru</h3></div>
    <table class="table">
        <thead><tr><th>#</th><th>Siswa</th><th>Item</th><th>Total</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($recentOrders as $order)
        <tr>
            <td style="font-weight: 700;">#{{ $order->id }}</td>
            <td>{{ $order->user->name }}</td>
            <td>{{ $order->items->count() }} item</td>
            <td style="font-weight: 700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td style="color: #64748b;">{{ $order->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">Belum ada pesanan hari ini</td></tr>
        @endforelse
        </tbody>
    </table>
</div>

@else
{{-- STUDENT DASHBOARD --}}
<div class="grid grid-2" style="margin-bottom: 1.5rem;">
    <div class="balance-card">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem;">
            <div>
                <p style="font-size: 0.85rem; opacity: 0.8; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Saldo Kartu RFID</p>
                <h2 style="font-size: 2.5rem; font-weight: 800; margin-top: 0.5rem;">Rp {{ number_format($card->balance ?? 0, 0, ',', '.') }}</h2>
            </div>
            <div style="background: rgba(255,255,255,0.2); padding: 0.75rem; border-radius: 12px;"><i data-lucide="credit-card" size="24" color="white"></i></div>
        </div>
        <div style="display: flex; gap: 1rem; align-items: center; margin-top: 1rem;" class="mobile-flex-wrap">
            @if($card)
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white; font-family: monospace;">UID: {{ $card->rfid_uid }}</span>
                <span style="display: flex; align-items: center; gap: 0.35rem; font-size: 0.8rem; color: rgba(255,255,255,0.8);"><span style="width: 8px; height: 8px; border-radius: 50%; background: #4ade80;"></span> Aktif</span>
            @else
                <span class="badge" style="background: rgba(255,255,255,0.2); color: white;">Belum terdaftar</span>
            @endif
            <button id="scan-nfc-btn" class="btn" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3); padding: 0.4rem 0.8rem; font-size: 0.75rem; display: flex; align-items: center; gap: 0.4rem;">
                <i data-lucide="nfc" size="14"></i> Scan Kartu via HP
            </button>
        </div>
    </div>
    <div class="grid" style="grid-template-columns: 1fr; gap: 0.75rem;">
        <div class="stat-card">
            <div class="stat-icon" style="background: #fee2e2; color: #ef4444;"><i data-lucide="shopping-cart" size="22"></i></div>
            <div><div class="stat-label">Total Belanja</div><div class="stat-value">Rp {{ number_format($totalSpent, 0, ',', '.') }}</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i data-lucide="receipt" size="22"></i></div>
            <div><div class="stat-label">Total Transaksi</div><div class="stat-value">{{ $recentOrders->count() }}</div></div>
        </div>
    </div>
</div>

<div class="table-container">
    <div style="padding: 1.25rem; border-bottom: 1px solid #e2e8f0;"><h3 style="font-weight: 700;">Transaksi Terakhir</h3></div>
    <table class="table">
        <thead><tr><th>Order</th><th>Item</th><th>Total</th><th>Status</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($recentOrders as $order)
        <tr>
            <td style="font-weight: 700;">#{{ $order->id }}</td>
            <td>@foreach($order->items as $item) {{ $item->menuItem->name ?? '-' }} (x{{ $item->quantity }}){{ !$loop->last ? ', ' : '' }} @endforeach</td>
            <td style="font-weight: 700;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-success">{{ ucfirst($order->status) }}</span></td>
            <td style="color: #64748b;">{{ $order->created_at->diffForHumans() }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="empty-state">Belum ada transaksi</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endif

<script>
    lucide.createIcons();

    // NFC Functionality
    const scanBtn = document.getElementById('scan-nfc-btn');
    if (scanBtn) {
        scanBtn.addEventListener('click', async () => {
            if (!('NDEFReader' in window)) {
                alert('Maaf, browser Anda tidak mendukung fitur Web NFC. Gunakan Chrome di Android.');
                return;
            }

            try {
                scanBtn.disabled = true;
                scanBtn.innerHTML = '<i data-lucide="loader" class="spin" size="14"></i> Mendengarkan...';
                lucide.createIcons();

                const ndef = new NDEFReader();
                await ndef.scan();
                
                alert('Silakan tempelkan kartu ke bagian belakang HP Anda.');

                ndef.addEventListener("readingerror", () => {
                    alert("Gagal membaca kartu. Coba lagi.");
                    resetScanBtn();
                });

                ndef.addEventListener("reading", ({ message, serialNumber }) => {
                    const uid = serialNumber.replace(/:/g, '').toUpperCase();
                    fetchBalance(uid);
                });

            } catch (error) {
                console.error(error);
                alert("Error: " + error);
                resetScanBtn();
            }
        });
    }

    function resetScanBtn() {
        if (scanBtn) {
            scanBtn.disabled = false;
            scanBtn.innerHTML = '<i data-lucide="nfc" size="14"></i> Scan Kartu via HP';
            lucide.createIcons();
        }
    }

    async function fetchBalance(uid) {
        try {
            const response = await fetch(`{{ route('nfc.check') }}?uid=${uid}`);
            const data = await response.json();
            
            if (data.success) {
                alert(`📋 Info Kartu\n\nNama: ${data.user_name}\nSaldo: Rp ${data.balance}\nStatus: ${data.is_active ? 'Aktif' : 'Nonaktif'}`);
            } else {
                alert(`❌ ${data.message}\nUID: ${uid}`);
            }
        } catch (error) {
            alert('Gagal mengambil data dari server.');
        } finally {
            resetScanBtn();
        }
    }
</script>
@endsection

@extends('layouts.app')
@section('page-title', 'Top-Up Saldo')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Top-Up Saldo RFID</h1>
        <p style="color:#64748b;font-size:0.875rem;">Isi saldo kartu RFID siswa</p>
    </div>
    <button onclick="document.getElementById('modal-topup').classList.add('active')" class="btn btn-primary">
        <i data-lucide="plus-circle" size="18"></i> Top-Up Baru
    </button>
</div>

<div class="table-container">
    <table class="table">
        <thead><tr><th>Siswa</th><th>Jumlah</th><th>Metode</th><th>Status</th><th>Diproses oleh</th><th>Waktu</th></tr></thead>
        <tbody>
        @forelse($topups as $topup)
        <tr>
            <td style="font-weight:600;">{{ $topup->user->name }}</td>
            <td style="font-weight:700;color:#16a34a;">+Rp {{ number_format($topup->amount, 0, ',', '.') }}</td>
            <td><span class="badge badge-info" style="text-transform:capitalize;">{{ $topup->method }}</span></td>
            <td><span class="badge badge-{{ $topup->status === 'success' ? 'success' : ($topup->status === 'pending' ? 'warning' : 'danger') }}">{{ ucfirst($topup->status) }}</span></td>
            <td style="color:#64748b;">{{ $topup->approvedBy->name ?? '-' }}</td>
            <td style="color:#64748b;">{{ $topup->created_at->format('d M Y, H:i') }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state"><i data-lucide="wallet" size="40"></i><p>Belum ada riwayat top-up</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Top-Up -->
<div class="modal-overlay" id="modal-topup">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;">Top-Up Saldo</h3>
            <button onclick="document.getElementById('modal-topup').classList.remove('active')" style="background:none;border:none;cursor:pointer;color:#94a3b8;"><i data-lucide="x" size="20"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.topup.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Siswa</label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} — Saldo: Rp {{ number_format($student->rfidCard->balance ?? 0, 0, ',', '.') }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Jumlah Top-Up (Rp)</label>
                <input type="number" name="amount" class="form-input" placeholder="Minimal Rp 1.000" min="1000" required>
            </div>
            <div class="form-group">
                <label class="form-label">Metode Pembayaran</label>
                <select name="method" class="form-select" required>
                    <option value="cash">💵 Cash / Tunai</option>
                    <option value="transfer">🏦 Transfer Bank</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">Proses Top-Up</button>
        </form>
    </div>
</div>

<script>lucide.createIcons();</script>
@endsection

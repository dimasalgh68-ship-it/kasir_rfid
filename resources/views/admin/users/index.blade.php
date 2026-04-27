@extends('layouts.app')
@section('page-title', 'Pengguna')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Manajemen Pengguna</h1>
        <p style="color:#64748b;font-size:0.875rem;">Kelola admin, petugas kantin, dan siswa</p>
    </div>
    <button onclick="document.getElementById('modal-user').classList.add('active')" class="btn btn-primary">
        <i data-lucide="user-plus" size="18"></i> Tambah Pengguna
    </button>
</div>

<div class="table-container">
    <table class="table">
        <thead><tr><th>Nama</th><th>Email</th><th>Role</th><th>Kartu RFID</th><th>Saldo</th><th>Aksi</th></tr></thead>
        <tbody>
        @foreach($users as $user)
        <tr>
            <td style="font-weight:600;">{{ $user->name }}</td>
            <td style="color:#64748b;">{{ $user->email }}</td>
            <td>
                <span class="badge {{ $user->role === 'admin' ? 'badge-danger' : ($user->role === 'canteen' ? 'badge-warning' : 'badge-info') }}" style="text-transform:capitalize;">{{ $user->role }}</span>
            </td>
            <td>
                @if($user->rfidCard)
                    <code style="background:#f1f5f9;padding:0.2rem 0.5rem;border-radius:4px;font-size:0.8rem;">{{ $user->rfidCard->rfid_uid }}</code>
                @else
                    <span style="color:#94a3b8;font-size:0.8rem;">—</span>
                @endif
            </td>
            <td style="font-weight:700;">{{ $user->rfidCard ? 'Rp '.number_format($user->rfidCard->balance, 0, ',', '.') : '—' }}</td>
            <td>
                @if($user->id !== Auth::id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus pengguna ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding:0.4rem 0.75rem;font-size:0.8rem;">Hapus</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<!-- Modal Tambah User -->
<div class="modal-overlay" id="modal-user">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;">Tambah Pengguna</h3>
            <button onclick="document.getElementById('modal-user').classList.remove('active')" style="background:none;border:none;cursor:pointer;color:#94a3b8;"><i data-lucide="x" size="20"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" minlength="6" required>
            </div>
            <div class="form-group">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>
                    <option value="student">👨‍🎓 Siswa</option>
                    <option value="canteen">🍽️ Petugas Kantin</option>
                    <option value="admin">🛡️ Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">Simpan</button>
        </form>
    </div>
</div>

<script>lucide.createIcons();</script>
@endsection

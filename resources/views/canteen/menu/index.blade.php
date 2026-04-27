@extends('layouts.app')
@section('page-title', 'Menu Kantin')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Menu Kantin</h1>
        <p style="color:#64748b;font-size:0.875rem;">Kelola makanan, minuman, dan snack kantin</p>
    </div>
    <button onclick="document.getElementById('modal-menu').classList.add('active')" class="btn btn-primary">
        <i data-lucide="plus" size="18"></i> Tambah Menu
    </button>
</div>

<div class="grid grid-3">
    @forelse($menuItems as $item)
    <div class="card-premium" style="position:relative;">
        @if($item->image)
            <img src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->name }}" style="width:100%;height:160px;object-fit:cover;border-radius:12px;margin-bottom:1rem;">
        @else
            <div style="width:100%;height:160px;border-radius:12px;margin-bottom:1rem;background:linear-gradient(135deg,#f1f5f9,#e2e8f0);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="utensils" size="40" style="color:#94a3b8;"></i>
            </div>
        @endif
        <div style="position:absolute;top:1rem;right:1rem;">
            @if($item->is_available)
                <span class="badge badge-success">Tersedia</span>
            @else
                <span class="badge badge-danger">Habis</span>
            @endif
        </div>
        <span class="badge badge-info" style="margin-bottom:0.5rem;text-transform:capitalize;">{{ $item->category }}</span>
        <h3 style="font-weight:700;font-size:1.1rem;margin-bottom:0.35rem;">{{ $item->name }}</h3>
        <p style="color:#64748b;font-size:0.8rem;margin-bottom:0.75rem;line-height:1.5;">{{ $item->description ?? 'Tanpa deskripsi' }}</p>
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <span style="font-weight:800;font-size:1.2rem;color:#6366f1;">Rp {{ number_format($item->price, 0, ',', '.') }}</span>
            <span style="font-size:0.8rem;color:#64748b;">Stok: {{ $item->stock }}</span>
        </div>
        <div style="display:flex;gap:0.5rem;">
            <form method="POST" action="{{ route('canteen.menu.toggle', $item) }}" style="flex:1;">
                @csrf @method('PATCH')
                <button type="submit" class="btn {{ $item->is_available ? 'btn-outline' : 'btn-success' }}" style="width:100%;justify-content:center;font-size:0.8rem;">
                    {{ $item->is_available ? 'Nonaktifkan' : 'Aktifkan' }}
                </button>
            </form>
            <form method="POST" action="{{ route('canteen.menu.destroy', $item) }}" onsubmit="return confirm('Hapus menu ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger" style="font-size:0.8rem;">
                    <i data-lucide="trash-2" size="14"></i>
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="card-premium" style="grid-column:1/-1;text-align:center;padding:3rem;">
        <i data-lucide="utensils-crossed" size="48" style="color:#94a3b8;opacity:0.3;margin-bottom:1rem;"></i>
        <p style="color:#94a3b8;">Belum ada menu. Tambahkan menu pertama Anda!</p>
    </div>
    @endforelse
</div>

<!-- Modal Tambah Menu -->
<div class="modal-overlay" id="modal-menu">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;">Tambah Menu Baru</h3>
            <button onclick="document.getElementById('modal-menu').classList.remove('active')" style="background:none;border:none;cursor:pointer;color:#94a3b8;"><i data-lucide="x" size="20"></i></button>
        </div>
        <form method="POST" action="{{ route('canteen.menu.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Menu</label>
                <input type="text" name="name" class="form-input" placeholder="Contoh: Nasi Goreng Spesial" required>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="price" class="form-input" placeholder="15000" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stock" class="form-input" placeholder="50" required>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Kategori</label>
                <select name="category" class="form-select" required>
                    <option value="makanan">🍚 Makanan</option>
                    <option value="minuman">🥤 Minuman</option>
                    <option value="snack">🍿 Snack</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi</label>
                <input type="text" name="description" class="form-input" placeholder="Deskripsi singkat menu">
            </div>
            <div class="form-group">
                <label class="form-label">Gambar (opsional)</label>
                <input type="file" name="image" class="form-input" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">Simpan Menu</button>
        </form>
    </div>
</div>

<script>lucide.createIcons();</script>
@endsection

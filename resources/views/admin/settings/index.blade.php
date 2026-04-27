@extends('layouts.app')
@section('page-title', 'Pengaturan Sistem')
@section('content')

<div style="margin-bottom:2rem;">
    <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Pengaturan Sistem</h1>
    <p style="color:#64748b;font-size:0.875rem;">Kelola konfigurasi API Midtrans dan aplikasi</p>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST">
    @csrf
    @method('PATCH')

    <div style="display:grid;grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));gap:1.5rem;">
        
        <!-- Midtrans Settings -->
        <div class="card" style="padding:1.5rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                <div style="background:#eff6ff;color:#3b82f6;padding:0.5rem;border-radius:0.5rem;">
                    <i data-lucide="credit-card" size="20"></i>
                </div>
                <h3 style="margin:0;font-size:1.1rem;">Konfigurasi Midtrans</h3>
            </div>

            <div class="form-group">
                <label class="form-label">Midtrans Server Key</label>
                <input type="password" name="midtrans_server_key" class="form-input" value="{{ $settings['midtrans']->where('key', 'midtrans_server_key')->first()->value ?? '' }}" placeholder="SB-Mid-server-...">
            </div>

            <div class="form-group">
                <label class="form-label">Midtrans Client Key</label>
                <input type="text" name="midtrans_client_key" class="form-input" value="{{ $settings['midtrans']->where('key', 'midtrans_client_key')->first()->value ?? '' }}" placeholder="SB-Mid-client-...">
            </div>

            <div class="form-group">
                <label class="form-label">Environment</label>
                <select name="midtrans_is_production" class="form-select">
                    <option value="0" {{ ($settings['midtrans']->where('key', 'midtrans_is_production')->first()->value ?? '0') == '0' ? 'selected' : '' }}>Sandbox (Testing)</option>
                    <option value="1" {{ ($settings['midtrans']->where('key', 'midtrans_is_production')->first()->value ?? '0') == '1' ? 'selected' : '' }}>Production (Live)</option>
                </select>
                <small style="color:#64748b;display:block;margin-top:0.25rem;">Gunakan Sandbox untuk simulasi pembayaran.</small>
            </div>
        </div>

        <!-- General Settings -->
        <div class="card" style="padding:1.5rem;">
            <div style="display:flex;align-items:center;gap:0.75rem;margin-bottom:1.25rem;">
                <div style="background:#f0fdf4;color:#16a34a;padding:0.5rem;border-radius:0.5rem;">
                    <i data-lucide="settings" size="20"></i>
                </div>
                <h3 style="margin:0;font-size:1.1rem;">Pengaturan Umum</h3>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Aplikasi</label>
                <input type="text" name="app_name" class="form-input" value="{{ $settings['general']->where('key', 'app_name')->first()->value ?? '' }}" placeholder="Kantin Digital RFID">
            </div>

            <div style="background:#fff7ed;border-left:4px solid #f97316;padding:1rem;border-radius:0.25rem;margin-top:1rem;">
                <p style="margin:0;font-size:0.875rem;color:#9a3412;">
                    <strong>Info:</strong> Pengaturan ini akan menimpa nilai default dari file .env. Pastikan data yang dimasukkan valid agar integrasi pembayaran tidak terputus.
                </p>
            </div>
        </div>

    </div>

    <div style="margin-top:2rem;display:flex;justify-content:flex-end;">
        <button type="submit" class="btn btn-primary" style="padding:0.75rem 2rem;">
            <i data-lucide="save" size="18" style="margin-right:0.5rem;"></i> Simpan Perubahan
        </button>
    </div>
</form>

<script>
    lucide.createIcons();
</script>
@endsection

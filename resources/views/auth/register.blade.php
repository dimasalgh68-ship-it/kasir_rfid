<x-guest-layout>
    <div class="auth-card-header">
        <h2>Buat Akun Baru ✨</h2>
        <p>Daftar untuk mulai menggunakan KantinKu</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="field">
            <label>Nama Lengkap <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Ahmad Fauzi" required autofocus autocomplete="name">
            </div>
            @error('name')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Email <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="username">
            </div>
            @error('email')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Password <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="password" type="password" name="password" placeholder="Min. 8 karakter" required autocomplete="new-password">
            </div>
            @error('password')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Konfirmasi Password <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ulangi password" required autocomplete="new-password">
            </div>
            @error('password_confirmation')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-auth" style="margin-top: 0.25rem;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:static!important;width:18px!important;height:18px!important;color:white!important;pointer-events:auto!important;transform:none!important"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
            Daftar Sekarang
        </button>
    </form>

    <div class="auth-divider"><span>atau</span></div>

    <div class="auth-footer">
        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>
    </div>
</x-guest-layout>

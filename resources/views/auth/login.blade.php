<x-guest-layout>
    <div class="auth-card-header">
        <h2>Selamat Datang 👋</h2>
        <p>Masuk ke akun KantinKu Anda</p>
    </div>

    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="field">
            <label>Email <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="username">
            </div>
            @error('email')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label>Password <span class="req">*</span></label>
            <div class="input-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input id="password" type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
            </div>
            @error('password')<div class="input-error">{{ $message }}</div>@enderror
        </div>

        <div class="field-row">
            <label class="remember">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Ingat saya</span>
            </label>
            @if (Route::has('password.request'))
                <a class="forgot" href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>

        <button type="submit" class="btn-auth">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position:static!important;width:18px!important;height:18px!important;color:white!important;pointer-events:auto!important;transform:none!important"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" x2="3" y1="12" y2="12"/></svg>
            Masuk
        </button>
    </form>

    <div class="auth-divider"><span>atau</span></div>

    <div class="auth-footer">
        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
    </div>
</x-guest-layout>

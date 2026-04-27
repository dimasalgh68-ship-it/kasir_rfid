<x-guest-layout>
    <div class="auth-header">
        <h2>Lupa Password? 🔐</h2>
        <p>Masukkan email Anda dan kami akan mengirimkan link reset password.</p>
    </div>

    @if (session('status'))
        <div class="session-status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="auth-form">
        @csrf

        <div class="field">
            <label>Email <span class="req">*</span></label>
            <div class="input-wrap">
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                <i data-lucide="mail" size="18"></i>
            </div>
            @error('email')
                <div class="input-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-auth">
            <i data-lucide="send" size="18"></i>
            Kirim Link Reset
        </button>
    </form>

    <div class="auth-footer" style="margin-top: 2rem;">
        <p>Ingat password? <a href="{{ route('login') }}">Kembali masuk</a></p>
    </div>

    <script>lucide.createIcons();</script>
</x-guest-layout>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KantinKu - Smart Canteen RFID System</title>
    <meta name="description" content="Sistem pembayaran kantin digital berbasis RFID-RC522 dan ESP32. Transaksi cepat, aman, dan cashless.">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Plus Jakarta Sans',sans-serif;overflow-x:hidden;background:#fff}
        .nav{position:fixed;top:0;left:0;right:0;z-index:1000;padding:1rem 5%;display:flex;justify-content:space-between;align-items:center;background:rgba(255,255,255,0.85);backdrop-filter:blur(20px);border-bottom:1px solid rgba(0,0,0,0.05)}
        .nav-brand{display:flex;align-items:center;gap:0.75rem;text-decoration:none}
        .nav-brand-icon{width:40px;height:40px;border-radius:12px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;color:white}
        .nav-brand span{font-weight:800;font-size:1.4rem;color:#1e293b;letter-spacing:-0.03em}
        .nav-links{display:flex;align-items:center;gap:2rem}
        .nav-links a{text-decoration:none;font-weight:600;font-size:0.9rem;color:#64748b;transition:color 0.2s}
        .nav-links a:hover{color:#6366f1}
        .btn-cta{display:inline-flex;align-items:center;gap:0.5rem;padding:0.7rem 1.75rem;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;border-radius:12px;font-weight:700;font-size:0.9rem;text-decoration:none;transition:all 0.3s;border:none;cursor:pointer}
        .btn-cta:hover{transform:translateY(-2px);box-shadow:0 8px 25px rgba(99,102,241,0.4)}
        .btn-outline-w{padding:0.7rem 1.75rem;background:white;color:#1e293b;border-radius:12px;font-weight:700;font-size:0.9rem;text-decoration:none;border:1px solid #e2e8f0;transition:all 0.3s}
        .btn-outline-w:hover{border-color:#6366f1;color:#6366f1}
        .hero{min-height:100vh;display:flex;align-items:center;padding:6rem 5% 4rem;position:relative;background:radial-gradient(circle at top right,rgba(99,102,241,0.08),transparent 50%),radial-gradient(circle at bottom left,rgba(139,92,246,0.05),transparent 50%)}
        .hero-content{max-width:650px}
        .hero-badge{display:inline-flex;align-items:center;gap:0.5rem;background:rgba(99,102,241,0.1);color:#6366f1;padding:0.5rem 1rem;border-radius:999px;font-size:0.85rem;font-weight:700;margin-bottom:2rem}
        .hero-title{font-size:clamp(2.5rem,5vw,4rem);font-weight:800;line-height:1.1;letter-spacing:-0.03em;margin-bottom:1.5rem;color:#0f172a}
        .hero-title span{background:linear-gradient(135deg,#6366f1,#8b5cf6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
        .hero-desc{font-size:1.1rem;color:#64748b;line-height:1.8;margin-bottom:2.5rem;max-width:550px}
        .hero-btns{display:flex;gap:1rem;flex-wrap:wrap}
        .hero-visual{position:absolute;right:8%;top:50%;transform:translateY(-50%);width:380px;animation:float 6s ease-in-out infinite}
        @keyframes float{0%,100%{transform:translateY(-50%)}50%{transform:translateY(calc(-50% - 20px))}}
        .card-demo{background:white;border-radius:20px;padding:2rem;box-shadow:0 20px 60px rgba(0,0,0,0.1);border:1px solid #e2e8f0}
        .features{padding:6rem 5%;background:#f8fafc}
        .section-header{text-align:center;max-width:600px;margin:0 auto 4rem}
        .section-header h2{font-size:2.25rem;font-weight:800;color:#0f172a;margin-bottom:1rem}
        .section-header p{font-size:1.05rem;color:#64748b}
        .features-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:2rem;max-width:1100px;margin:0 auto}
        .feature-card{background:white;border-radius:20px;padding:2.5rem 2rem;border:1px solid #e2e8f0;transition:all 0.3s}
        .feature-card:hover{transform:translateY(-5px);box-shadow:0 15px 40px rgba(0,0,0,0.08)}
        .feature-icon{width:56px;height:56px;border-radius:16px;display:flex;align-items:center;justify-content:center;margin-bottom:1.5rem;color:white}
        .feature-card h3{font-size:1.25rem;font-weight:700;margin-bottom:0.75rem;color:#1e293b}
        .feature-card p{color:#64748b;line-height:1.7;font-size:0.95rem}
        .how-it-works{padding:6rem 5%}
        .steps{display:grid;grid-template-columns:repeat(3,1fr);gap:3rem;max-width:1000px;margin:0 auto}
        .step{text-align:center;position:relative}
        .step-num{width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;display:flex;align-items:center;justify-content:center;font-size:1.5rem;font-weight:800;margin:0 auto 1.5rem;box-shadow:0 8px 25px rgba(99,102,241,0.3)}
        .step h3{font-size:1.1rem;font-weight:700;margin-bottom:0.5rem;color:#1e293b}
        .step p{color:#64748b;font-size:0.9rem;line-height:1.6}
        .cta-section{padding:5rem;text-align:center;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:white;margin:3rem 5%;border-radius:24px;position:relative;overflow:hidden}
        .cta-section::before{content:'';position:absolute;top:-50%;right:-20%;width:400px;height:400px;border-radius:50%;background:rgba(255,255,255,0.08)}
        .cta-section h2{font-size:2rem;font-weight:800;margin-bottom:1rem;position:relative}
        .cta-section p{opacity:0.85;margin-bottom:2rem;font-size:1.05rem;position:relative}
        footer{background:#0f172a;color:white;padding:4rem 5% 2rem}
        .footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr;gap:3rem;margin-bottom:3rem}
        .footer-brand{display:flex;align-items:center;gap:0.75rem;margin-bottom:1rem}
        .footer-brand span{font-weight:800;font-size:1.25rem}
        footer p{color:#94a3b8;font-size:0.85rem;line-height:1.7}
        footer h4{margin-bottom:1rem;font-weight:700}
        footer ul{list-style:none;padding:0;display:flex;flex-direction:column;gap:0.5rem}
        footer ul a{color:#94a3b8;text-decoration:none;font-size:0.85rem;transition:color 0.2s}
        footer ul a:hover{color:white}
        .footer-bottom{border-top:1px solid rgba(255,255,255,0.1);padding-top:1.5rem;display:flex;justify-content:space-between;align-items:center}
        .footer-bottom p{color:#64748b;font-size:0.8rem}
        @media(max-width:768px){.hero-visual{display:none}.features-grid,.steps,.footer-grid{grid-template-columns:1fr}.nav-links{display:none}}
    </style>
</head>
<body>
    <nav class="nav">
        <a href="/" class="nav-brand">
            <div class="nav-brand-icon"><i data-lucide="utensils" size="22"></i></div>
            <span>KantinKu</span>
        </a>
        <div class="nav-links">
            <a href="#fitur">Fitur</a>
            <a href="#cara-kerja">Cara Kerja</a>
            @if(Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-cta">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" style="font-weight:700;color:#1e293b">Masuk</a>
                    @if(Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-cta">Daftar Sekarang</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-badge"><i data-lucide="zap" size="16"></i> Sistem RFID + ESP32</div>
            <h1 class="hero-title">Kantin Digital<br><span>Tanpa Uang Tunai.</span></h1>
            <p class="hero-desc">Sistem pembayaran kantin berbasis RFID-RC522 dan ESP32. Cukup tap kartu, saldo terpotong otomatis. Cepat, aman, dan transparan.</p>
            <div class="hero-btns">
                <a href="{{ route('register') }}" class="btn-cta" style="padding:1rem 2.5rem;font-size:1rem">Mulai Sekarang <i data-lucide="arrow-right" size="18"></i></a>
                <a href="#fitur" class="btn-outline-w" style="padding:1rem 2.5rem;font-size:1rem">Lihat Fitur</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="card-demo">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem">
                    <div style="background:#f1f5f9;padding:0.5rem;border-radius:10px"><i data-lucide="credit-card" size="24" color="#6366f1"></i></div>
                    <span style="background:#dcfce7;color:#16a34a;padding:0.25rem 0.75rem;border-radius:999px;font-size:0.75rem;font-weight:700">AKTIF</span>
                </div>
                <p style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.35rem">Saldo Kartu</p>
                <h2 style="font-size:2rem;font-weight:800;color:#1e293b;margin-bottom:1.5rem">Rp 185.000</h2>
                <div style="padding-top:1rem;border-top:1px solid #f1f5f9">
                    <div style="display:flex;align-items:center;gap:0.75rem">
                        <div style="width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,#6366f1,#8b5cf6)"></div>
                        <div>
                            <div style="font-size:0.85rem;font-weight:600;color:#1e293b">Ahmad Fauzi</div>
                            <div style="font-size:0.7rem;color:#94a3b8;font-family:monospace">UID: A3F2B1C4</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="fitur" class="features">
        <div class="section-header">
            <h2>Fitur Unggulan</h2>
            <p>Solusi pembayaran kantin yang terintegrasi dengan hardware RFID dan ESP32 untuk pengalaman cashless.</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#6366f1,#8b5cf6)"><i data-lucide="scan-line" size="28"></i></div>
                <h3>RFID-RC522 Scan</h3>
                <p>Pembayaran instan dengan tap kartu RFID. Modul RC522 membaca UID dalam milidetik untuk transaksi super cepat.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#10b981,#059669)"><i data-lucide="cpu" size="28"></i></div>
                <h3>ESP32 Controller</h3>
                <p>Mikrokontroler ESP32 mengirim data kartu ke server via WiFi. Koneksi stabil dengan buzzer feedback.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#f59e0b,#d97706)"><i data-lucide="bar-chart-3" size="28"></i></div>
                <h3>Dashboard Real-time</h3>
                <p>Pantau semua transaksi, saldo, dan aktivitas kantin secara real-time melalui dashboard web.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#ef4444,#dc2626)"><i data-lucide="shield-check" size="28"></i></div>
                <h3>Keamanan UID</h3>
                <p>Setiap kartu memiliki UID unik yang tidak bisa dipalsukan. Kartu bisa dinonaktifkan jika hilang.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#06b6d4,#0891b2)"><i data-lucide="wallet" size="28"></i></div>
                <h3>Top-Up Saldo</h3>
                <p>Admin dapat mengisi saldo kartu RFID siswa langsung dari dashboard. Riwayat top-up tercatat rapi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon" style="background:linear-gradient(135deg,#8b5cf6,#7c3aed)"><i data-lucide="utensils-crossed" size="28"></i></div>
                <h3>Menu Kantin</h3>
                <p>Kelola menu makanan dan minuman kantin dengan harga, stok, dan kategori. Mudah diupdate kapan saja.</p>
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="how-it-works">
        <div class="section-header">
            <h2>Cara Kerja</h2>
            <p>Proses transaksi kantin digital dalam 3 langkah sederhana</p>
        </div>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Tap Kartu RFID</h3>
                <p>Siswa mendekatkan kartu RFID ke reader RC522 yang terhubung dengan ESP32.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Verifikasi & Bayar</h3>
                <p>ESP32 mengirim UID ke server Laravel. Saldo dicek dan dipotong otomatis.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Konfirmasi Buzzer</h3>
                <p>Buzzer berbunyi sebagai konfirmasi. Transaksi tercatat di dashboard.</p>
            </div>
        </div>
    </section>

    <section class="cta-section">
        <h2>Siap Digitalisasi Kantin Anda?</h2>
        <p>Mulai gunakan sistem pembayaran RFID untuk kantin sekolah Anda sekarang.</p>
        <a href="{{ route('register') }}" class="btn-cta" style="background:white;color:#6366f1;padding:1rem 2.5rem;font-size:1rem;position:relative">Daftar Gratis <i data-lucide="arrow-right" size="18"></i></a>
    </section>

    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <div style="background:white;width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#6366f1"><i data-lucide="utensils" size="18"></i></div>
                    <span>KantinKu</span>
                </div>
                <p>Sistem pembayaran kantin digital berbasis RFID-RC522 dan ESP32 untuk lingkungan sekolah modern.</p>
            </div>
            <div>
                <h4>Fitur</h4>
                <ul><li><a href="#">Kartu RFID</a></li><li><a href="#">Top-Up Saldo</a></li><li><a href="#">Menu Kantin</a></li><li><a href="#">Laporan</a></li></ul>
            </div>
            <div>
                <h4>Info</h4>
                <ul><li><a href="#">Tentang Kami</a></li><li><a href="#">Kontak</a></li><li><a href="#">Kebijakan Privasi</a></li></ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 KantinKu. Dibuat dengan ❤️</p>
        </div>
    </footer>
    <script>lucide.createIcons();</script>
</body>
</html>

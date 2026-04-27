@extends('layouts.app')
@section('page-title', 'Kartu RFID')
@section('content')

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <div>
        <h1 style="font-size:1.5rem;font-weight:800;color:#1e293b;">Manajemen Kartu NFC/RFID</h1>
        <p style="color:#64748b;font-size:0.875rem;">Daftarkan dan kelola kartu/tag NFC siswa</p>
    </div>
    <button onclick="document.getElementById('modal-register').classList.add('active')" class="btn btn-primary">
        <i data-lucide="plus" size="18"></i> Daftarkan Kartu
    </button>
</div>

<div class="table-container">
    <table class="table">
        <thead><tr><th>Siswa</th><th>UID Kartu (NFC/RFID)</th><th>Saldo</th><th>Status</th><th>Terdaftar</th><th>Aksi</th></tr></thead>
        <tbody>
        @forelse($cards as $card)
        <tr>
            <td style="font-weight:600;">{{ $card->user->name }}</td>
            <td><code style="background:#f1f5f9;padding:0.25rem 0.5rem;border-radius:6px;font-size:0.8rem;">{{ $card->rfid_uid }}</code></td>
            <td style="font-weight:700;">Rp {{ number_format($card->balance, 0, ',', '.') }}</td>
            <td>
                @if($card->is_active)
                    <span class="badge badge-success">Aktif</span>
                @else
                    <span class="badge badge-danger">Nonaktif</span>
                @endif
            </td>
            <td style="color:#64748b;">{{ $card->created_at->format('d M Y') }}</td>
            <td style="display:flex;gap:0.5rem;">
                <form method="POST" action="{{ route('admin.rfid.toggle', $card) }}">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn {{ $card->is_active ? 'btn-outline' : 'btn-success' }}" style="padding:0.4rem 0.75rem;font-size:0.8rem;">
                        {{ $card->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.rfid.destroy', $card) }}" onsubmit="return confirm('Yakin hapus kartu ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="padding:0.4rem 0.75rem;font-size:0.8rem;">Hapus</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="6" class="empty-state"><i data-lucide="credit-card" size="40"></i><p>Belum ada kartu terdaftar</p></td></tr>
        @endforelse
        </tbody>
    </table>
</div>

<!-- Modal Register Card -->
<div class="modal-overlay" id="modal-register">
    <div class="modal">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
            <h3 style="margin:0;">Daftarkan Kartu NFC/RFID</h3>
            <button onclick="document.getElementById('modal-register').classList.remove('active')" style="background:none;border:none;cursor:pointer;color:#94a3b8;"><i data-lucide="x" size="20"></i></button>
        </div>
        <form method="POST" action="{{ route('admin.rfid.register') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Siswa</label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.35rem;">
                    <label class="form-label" style="margin-bottom:0;">UID Kartu NFC/RFID</label>
                    <span id="reader-status" style="font-size:0.7rem;color:#10b981;display:flex;align-items:center;gap:0.25rem;font-weight:600;">
                        <span style="width:6px;height:6px;background:#10b981;border-radius:50%;display:inline-block;animation:pulse 1.5s infinite;"></span>
                        Reader Ready
                    </span>
                </div>
                <div style="display:flex;gap:0.5rem;">
                    <input type="text" name="rfid_uid" id="reg_rfid_uid" class="form-input" placeholder="Tempelkan kartu ke reader..." required style="flex:1;">
                    <button type="button" id="scan-nfc-reg" class="btn btn-outline" style="padding:0.4rem 0.75rem;" title="Scan via HP (NFC)"><i data-lucide="scan" size="16"></i></button>
                </div>
                <small style="color:#94a3b8;font-size:0.75rem;">Tempelkan kartu ke reader ESP32 atau klik icon scan untuk scan via HP</small>
                
                <style>
                    @keyframes pulse {
                        0% { opacity: 1; }
                        50% { opacity: 0.3; }
                        100% { opacity: 1; }
                    }
                </style>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:0.5rem;">Daftarkan Kartu</button>
        </form>
    </div>
</div>

<script>
    lucide.createIcons();

    const scanBtn = document.getElementById('scan-nfc-reg');
    const uidInput = document.getElementById('reg_rfid_uid');

    // Firebase Listener for Tap to Register
    const firebaseConfig = {
        databaseURL: "{{ env('FIREBASE_DATABASE_URL') }}"
    };

    // Import Firebase
    const script = document.createElement('script');
    script.src = "https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js";
    document.head.appendChild(script);

    script.onload = () => {
        const dbScript = document.createElement('script');
        dbScript.src = "https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js";
        document.head.appendChild(dbScript);

        dbScript.onload = () => {
            firebase.initializeApp(firebaseConfig);
            const database = firebase.database();
            
            // Listen for latest scan
            database.ref('scans/latest').on('value', (snapshot) => {
                const data = snapshot.val();
                if (data && data.rfid_uid) {
                    uidInput.value = data.rfid_uid;
                    
                    // Visual feedback
                    uidInput.style.borderColor = '#10b981';
                    uidInput.style.backgroundColor = '#f0fdf4';
                    
                    setTimeout(() => {
                        uidInput.style.borderColor = '#e2e8f0';
                        uidInput.style.backgroundColor = 'white';
                    }, 2000);

                    // Show notification if modal is open
                    if (document.getElementById('modal-register').classList.contains('active')) {
                        console.log("Card detected: " + data.rfid_uid);
                    }
                }
            });
        };
    };

    if (scanBtn) {
        scanBtn.addEventListener('click', async () => {
            if (!('NDEFReader' in window)) {
                alert('Browser tidak mendukung Web NFC. Gunakan Chrome di Android.');
                return;
            }

            try {
                const ndef = new NDEFReader();
                await ndef.scan();
                
                scanBtn.innerHTML = '<i data-lucide="loader" class="spin" size="16"></i>';
                lucide.createIcons();
                
                alert('Dekatkan kartu ke HP untuk mengambil UID.');

                ndef.addEventListener("reading", ({ serialNumber }) => {
                    uidInput.value = serialNumber.replace(/:/g, '').toUpperCase();
                    scanBtn.innerHTML = '<i data-lucide="check" size="16"></i>';
                    lucide.createIcons();
                    setTimeout(() => {
                        scanBtn.innerHTML = '<i data-lucide="scan" size="16"></i>';
                        lucide.createIcons();
                    }, 2000);
                });
            } catch (error) {
                alert("Error: " + error);
            }
        });
    }
</script>
@endsection

@extends('layouts.app')
@section('page-title', 'Kasir Kantin')
@section('content')

<div style="display: grid; grid-template-columns: 1fr 400px; gap: 1.5rem; height: calc(100vh - 140px);">
    {{-- Left: Menu Selection --}}
    <div style="display: flex; flex-direction: column; gap: 1.5rem; overflow: hidden;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 style="font-size: 1.5rem; font-weight: 800; color: #1e293b;">Kasir Kantin</h1>
            <div style="display: flex; gap: 0.5rem;">
                <button class="category-btn active" data-category="all">Semua</button>
                @foreach($categories as $cat)
                <button class="category-btn" data-category="{{ $cat }}">{{ $cat }}</button>
                @endforeach
            </div>
        </div>

        <div id="menu-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; overflow-y: auto; padding-right: 0.5rem;">
            @foreach($menuItems as $item)
            <div class="menu-item-card" data-category="{{ $item->category }}" onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }})">
                <div style="aspect-ratio: 1; border-radius: 12px; background: #f1f5f9; margin-bottom: 0.75rem; overflow: hidden;">
                    @if($item->image)
                        <img src="{{ asset('storage/'.$item->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i data-lucide="utensils" size="32"></i></div>
                    @endif
                </div>
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.25rem;">{{ $item->name }}</div>
                <div style="color: #6366f1; font-weight: 800; font-size: 0.9rem;">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Cart & Payment --}}
    <div class="card-premium" style="display: flex; flex-direction: column; padding: 0; overflow: hidden; height: 100%;">
        <div style="padding: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; font-weight: 700;">Pesanan</h3>
            <button onclick="clearCart()" style="background: none; border: none; color: #ef4444; font-size: 0.8rem; cursor: pointer;">Hapus Semua</button>
        </div>

        <div id="cart-items" style="flex: 1; overflow-y: auto; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem;">
            {{-- Cart items will be injected here --}}
            <div id="empty-cart" style="text-align: center; color: #94a3b8; margin-top: 2rem;">
                <i data-lucide="shopping-cart" size="48" style="opacity: 0.2; margin-bottom: 1rem;"></i>
                <p>Belum ada item dipilih</p>
            </div>
        </div>

        <div style="padding: 1.5rem; background: #f8fafc; border-top: 1px solid #f1f5f9;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #64748b;">
                <span>Subtotal</span>
                <span id="subtotal">Rp 0</span>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 1.25rem; font-weight: 800; font-size: 1.25rem; color: #1e293b;">
                <span>Total</span>
                <span id="total">Rp 0</span>
            </div>

            <button id="pay-btn" onclick="showPaymentModal()" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem; border-radius: 12px; font-weight: 700;" disabled>
                Proses Pembayaran
            </button>
        </div>
    </div>
</div>

{{-- Payment Modal --}}
<div class="modal-overlay" id="modal-payment">
    <div class="modal" style="max-width: 400px; text-align: center; padding: 2.5rem;">
        <div id="payment-step-scan">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #e0e7ff; color: #6366f1; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i data-lucide="contactless" size="40" class="pulse"></i>
            </div>
            <h2 style="margin-bottom: 0.5rem;">Siap Scan Kartu</h2>
            <p style="color: #64748b; margin-bottom: 1.5rem;">Silakan tempelkan kartu RFID ke reader atau klik tombol scan via HP</p>
            
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <button onclick="startNfcScan()" class="btn btn-outline" style="width: 100%; justify-content: center; gap: 0.5rem;">
                    <i data-lucide="nfc" size="18"></i> Scan via HP
                </button>
                <button onclick="closePaymentModal()" class="btn" style="width: 100%; justify-content: center; background: none; color: #94a3b8;">Batal</button>
            </div>
        </div>

        <div id="payment-step-processing" style="display: none;">
            <div class="spin" style="margin: 0 auto 1.5rem; color: #6366f1;"><i data-lucide="loader-2" size="48"></i></div>
            <h2>Memproses...</h2>
            <p style="color: #64748b;">Harap tunggu sebentar</p>
        </div>

        <div id="payment-step-success" style="display: none;">
            <div style="width: 80px; height: 80px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
                <i data-lucide="check-circle-2" size="40"></i>
            </div>
            <h2 id="success-title">Berhasil!</h2>
            <p id="success-msg" style="color: #64748b; margin-bottom: 1.5rem;"></p>
            <button id="btn-reset-pos" class="btn btn-success" style="width: 100%; justify-content: center;">Selesai</button>
        </div>
    </div>
</div>

<style>
    .category-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .category-btn.active {
        background: #6366f1;
        color: white;
        border-color: #6366f1;
    }
    .menu-item-card {
        background: white;
        padding: 0.75rem;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        cursor: pointer;
        transition: all 0.2s;
    }
    .menu-item-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);
        border-color: #e0e7ff;
    }
    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .quantity-control {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        background: #f1f5f9;
        padding: 0.25rem;
        border-radius: 8px;
    }
    .qty-btn {
        width: 24px;
        height: 24px;
        border-radius: 6px;
        border: none;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #1e293b;
    }
    .pulse { animation: pulse 2s infinite; }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
        100% { transform: scale(1); opacity: 1; }
    }
    .spin { animation: spin 1s linear infinite; }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
</style>

<script>
    let cart = [];
    let total = 0;

    function addToCart(id, name, price) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            existing.qty++;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            renderCart();
        }
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const container = document.getElementById('cart-items');
        const empty = document.getElementById('empty-cart');
        
        if (cart.length === 0) {
            container.innerHTML = '';
            container.appendChild(empty);
            total = 0;
        } else {
            container.innerHTML = cart.map(item => `
                <div class="cart-item">
                    <div>
                        <div style="font-weight: 700; color: #1e293b;">${item.name}</div>
                        <div style="font-size: 0.85rem; color: #64748b;">Rp ${new Intl.NumberFormat('id-ID').format(item.price)}</div>
                    </div>
                    <div class="quantity-control">
                        <button class="qty-btn" onclick="updateQty(${item.id}, -1)"><i data-lucide="minus" size="14"></i></button>
                        <span style="font-weight: 700; min-width: 20px; text-align: center;">${item.qty}</span>
                        <button class="qty-btn" onclick="updateQty(${item.id}, 1)"><i data-lucide="plus" size="14"></i></button>
                    </div>
                </div>
            `).join('');
            lucide.createIcons();
            total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        document.getElementById('subtotal').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        document.getElementById('total').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        document.getElementById('pay-btn').disabled = cart.length === 0;
    }

    function resetPOS() {
        // Reset state
        cart = [];
        renderCart();
        
        // Reset UI steps
        document.getElementById('payment-step-success').style.display = 'none';
        document.getElementById('payment-step-scan').style.display = 'block';
        
        // Close modal
        closePaymentModal();
    }

    // Add explicit listener for the Done button to ensure it works
    document.addEventListener('DOMContentLoaded', () => {
        const resetBtn = document.getElementById('btn-reset-pos');
        if (resetBtn) {
            resetBtn.addEventListener('click', resetPOS);
        }
    });

    // Firebase Integration for Cashier
    const firebaseConfig = {
        databaseURL: "{{ env('FIREBASE_DATABASE_URL') }}"
    };

    // Load Firebase
    const fbScript = document.createElement('script');
    fbScript.src = "https://www.gstatic.com/firebasejs/9.22.0/firebase-app-compat.js";
    document.head.appendChild(fbScript);

    fbScript.onload = () => {
        const dbScript = document.createElement('script');
        dbScript.src = "https://www.gstatic.com/firebasejs/9.22.0/firebase-database-compat.js";
        document.head.appendChild(dbScript);

        dbScript.onload = () => {
            firebase.initializeApp(firebaseConfig);
            window.firebaseDB = firebase.database();
        };
    };

    function startFirebaseListener() {
        if (!window.firebaseDB) return;
        
        // Listen for latest scan
        window.firebaseDB.ref('scans/latest').on('value', (snapshot) => {
            const data = snapshot.val();
            // Hanya proses jika modal sedang terbuka di langkah scan
            const modalActive = document.getElementById('modal-payment').classList.contains('active');
            const stepScanActive = document.getElementById('payment-step-scan').style.display !== 'none';
            
            if (modalActive && stepScanActive && data && data.rfid_uid) {
                console.log("Card detected in Cashier: " + data.rfid_uid);
                processPayment(data.rfid_uid);
            }
        });
    }

    function stopFirebaseListener() {
        if (window.firebaseDB) {
            window.firebaseDB.ref('scans/latest').off();
        }
    }

    function showPaymentModal() {
        document.getElementById('modal-payment').classList.add('active');
        startFirebaseListener();
    }

    async function startNfcScan() {
        if (!('NDEFReader' in window)) {
            alert('Fitur NFC hanya tersedia di Chrome Android');
            return;
        }
        try {
            const ndef = new NDEFReader();
            await ndef.scan();
            alert('Tempelkan kartu ke HP Anda');
            ndef.addEventListener("reading", ({ serialNumber }) => {
                const uid = serialNumber.replace(/:/g, '').toUpperCase();
                processPayment(uid);
            });
        } catch (e) {
            alert('Gagal scan NFC: ' + e);
        }
    }

    async function processPayment(uid) {
        // Mencegah proses ganda jika sedang memproses
        if (document.getElementById('payment-step-processing').style.display === 'block') return;
        
        stopFirebaseListener();
        
        document.getElementById('payment-step-scan').style.display = 'none';
        document.getElementById('payment-step-processing').style.display = 'block';

        try {
            const response = await fetch('{{ route("canteen.cashier.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    rfid_uid: uid,
                    items: cart.map(i => ({ id: i.id, quantity: i.qty }))
                })
            });

            const data = await response.json();
            
            if (data.success) {
                document.getElementById('payment-step-processing').style.display = 'none';
                document.getElementById('payment-step-success').style.display = 'block';
                document.getElementById('success-msg').innerText = `Terima kasih ${data.user_name}!\nSisa Saldo: Rp ${new Intl.NumberFormat('id-ID').format(data.new_balance)}`;
                
                // Trigger sound or haptic if needed
                if (window.navigator.vibrate) window.navigator.vibrate(200);
            } else {
                alert(data.message);
                document.getElementById('payment-step-processing').style.display = 'none';
                document.getElementById('payment-step-scan').style.display = 'block';
                startFirebaseListener();
            }
        } catch (e) {
            console.error(e);
            alert('Terjadi kesalahan server');
            document.getElementById('payment-step-processing').style.display = 'none';
            document.getElementById('payment-step-scan').style.display = 'block';
            startFirebaseListener();
        }
    }

    function closePaymentModal() {
        document.getElementById('modal-payment').classList.remove('active');
        stopFirebaseListener();
    }

    // Filter Categories
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const cat = btn.dataset.category;
            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (cat === 'all' || card.dataset.category === cat) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>
@endsection

@extends('layouts.app')
@section('page-title', 'Kasir Kantin')
@section('content')

<div class="cashier-layout">
    {{-- Left: Menu Selection --}}
    <div class="menu-section">
        <div class="section-header">
            <h1 class="page-title">Kasir Kantin</h1>
            <div class="category-tabs">
                <button class="category-btn active" data-category="all">Semua</button>
                @foreach($categories as $cat)
                <button class="category-btn" data-category="{{ $cat }}">{{ $cat }}</button>
                @endforeach
            </div>
        </div>

        <div id="menu-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem; overflow-y: auto; padding-right: 0.5rem;">
            @foreach($menuItems as $item)
            <div class="menu-item-card {{ $item->stock <= 0 ? 'disabled' : '' }}" 
                 data-category="{{ $item->category }}" 
                 onclick="addToCart({{ $item->id }}, '{{ $item->name }}', {{ $item->price }}, {{ $item->stock }})">
                <div style="aspect-ratio: 1; border-radius: 12px; background: #f1f5f9; margin-bottom: 0.75rem; overflow: hidden; position: relative;">
                    @if($item->image)
                        <img src="{{ asset('storage/'.$item->image) }}" style="width: 100%; height: 100%; object-fit: cover; {{ $item->stock <= 0 ? 'filter: grayscale(1);' : '' }}">
                    @else
                        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; color: #94a3b8;"><i data-lucide="utensils" size="32"></i></div>
                    @endif

                    @if($item->stock <= 0)
                    <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: 0.8rem; text-transform: uppercase;">Habis</div>
                    @endif
                </div>
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div style="font-weight: 700; color: #1e293b; margin-bottom: 0.15rem;">{{ $item->name }}</div>
                        <div style="color: #6366f1; font-weight: 800; font-size: 0.9rem;">Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                    <div style="font-size: 0.65rem; color: #94a3b8; font-weight: 600; text-align: right;">
                        Stok: {{ $item->stock }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Right: Cart & Payment --}}
    <div class="cart-section card-premium">
        <div class="cart-header">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <button class="cart-close-mobile" onclick="toggleMobileCart()"><i data-lucide="arrow-left" size="20"></i></button>
                <h3 style="margin: 0; font-weight: 700;">Pesanan</h3>
            </div>
            <button onclick="clearCart()" style="background: none; border: none; color: #ef4444; font-size: 0.8rem; cursor: pointer;">Hapus Semua</button>
        </div>

        <div id="cart-items" class="cart-items-container">
            {{-- Cart items will be injected here --}}
            <div id="empty-cart" class="empty-cart-state">
                <i data-lucide="shopping-cart" size="48" style="opacity: 0.2; margin-bottom: 1rem;"></i>
                <p>Belum ada item dipilih</p>
            </div>
        </div>

        <div class="cart-footer">
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

<!-- Mobile Cart Toggle -->
<div class="mobile-cart-toggle" onclick="toggleMobileCart()">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
        <div style="position: relative;">
            <i data-lucide="shopping-cart"></i>
            <span id="mobile-cart-count" style="position: absolute; top: -8px; right: -8px; background: #ef4444; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; font-weight: bold;">0</span>
        </div>
        <span id="mobile-cart-total" style="font-weight: 800; color: #1e293b; margin-left: 0.5rem;">Rp 0</span>
    </div>
    <div style="font-weight: 700; color: #6366f1; background: #e0e7ff; padding: 0.5rem 1rem; border-radius: 8px;">Lihat Pesanan</div>
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
            <button onclick="resetPOS()" class="btn btn-success" style="width: 100%; justify-content: center;">Selesai</button>
        </div>
    </div>
</div>

<style>
    .cashier-layout {
        display: grid; 
        grid-template-columns: 1fr 400px; 
        gap: 1.5rem; 
        height: calc(100vh - 140px);
    }
    .menu-section {
        display: flex; 
        flex-direction: column; 
        gap: 1.5rem; 
        overflow: hidden;
    }
    .section-header {
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }
    .category-tabs {
        display: flex; 
        gap: 0.5rem;
        overflow-x: auto;
        padding-bottom: 0.25rem;
    }
    .cart-section {
        display: flex; 
        flex-direction: column; 
        padding: 0 !important; 
        overflow: hidden; 
        height: 100%;
    }
    .cart-header {
        padding: 1.25rem; 
        border-bottom: 1px solid #f1f5f9; 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
    }
    .cart-items-container {
        flex: 1; 
        overflow-y: auto; 
        padding: 1.25rem; 
        display: flex; 
        flex-direction: column; 
        gap: 1rem;
    }
    .cart-footer {
        padding: 1.5rem; 
        background: #f8fafc; 
        border-top: 1px solid #f1f5f9;
    }
    .empty-cart-state {
        text-align: center; 
        color: #94a3b8; 
        margin-top: 2rem;
    }
    .mobile-cart-toggle {
        display: none;
    }
    .cart-close-mobile {
        display: none;
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 0;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 1024px) {
        .cashier-layout {
            grid-template-columns: 1fr;
            height: auto;
            overflow: visible;
            padding-bottom: 80px; /* Space for the floating bottom bar */
        }
        .cart-section {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            z-index: 100;
            background: white;
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
            margin: 0;
            border-radius: 0;
        }
        .cart-section.open {
            transform: translateX(0);
        }
        .mobile-cart-toggle {
            display: flex;
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 1rem 1.5rem;
            box-shadow: 0 -4px 15px rgba(0,0,0,0.05);
            z-index: 40;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #f1f5f9;
            cursor: pointer;
        }
        .cart-header {
            padding-top: max(1.25rem, env(safe-area-inset-top));
        }
        .cart-close-mobile {
            display: flex;
        }
        .menu-item-card {
            margin-bottom: 0;
        }
        #menu-grid {
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)) !important;
        }
    }
    .category-btn {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        background: white;
        border: 1px solid #e2e8f0;
        color: #64748b;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        white-space: nowrap;
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

    function addToCart(id, name, price, stock) {
        const existing = cart.find(i => i.id === id);
        if (existing) {
            if (existing.qty >= stock) {
                alert('Stok tidak mencukupi!');
                return;
            }
            existing.qty++;
        } else {
            if (stock <= 0) {
                alert('Stok habis!');
                return;
            }
            cart.push({ id, name, price, qty: 1, stock });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        const item = cart.find(i => i.id === id);
        if (item) {
            if (delta > 0 && item.qty >= item.stock) {
                alert('Stok tidak mencukupi!');
                return;
            }
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

    const cartContainer = document.getElementById('cart-items');
    const emptyState = document.getElementById('empty-cart');

    function toggleMobileCart() {
        document.querySelector('.cart-section').classList.toggle('open');
        lucide.createIcons();
    }

    function renderCart() {
        let totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
        if (document.getElementById('mobile-cart-count')) {
            document.getElementById('mobile-cart-count').innerText = totalItems;
        }

        if (cart.length === 0) {
            cartContainer.innerHTML = '';
            cartContainer.appendChild(emptyState);
            emptyState.style.display = 'block';
            total = 0;
        } else {
            emptyState.style.display = 'none';
            cartContainer.innerHTML = '';
            cartContainer.appendChild(emptyState); // Keep it in DOM
            
            const itemsHtml = cart.map(item => `
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
            
            cartContainer.insertAdjacentHTML('beforeend', itemsHtml);
            lucide.createIcons();
            total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
        }

        document.getElementById('subtotal').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        document.getElementById('total').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        if (document.getElementById('mobile-cart-total')) {
            document.getElementById('mobile-cart-total').innerText = `Rp ${new Intl.NumberFormat('id-ID').format(total)}`;
        }
        document.getElementById('pay-btn').disabled = cart.length === 0;
    }

    function showPaymentModal() {
        document.getElementById('modal-payment').classList.add('active');
        // Listen for RFID from Firebase (Auto-poll check)
        startFirebasePolling();
    }

    function closePaymentModal() {
        document.getElementById('modal-payment').classList.remove('active');
        stopFirebasePolling();
    }

    let pollInterval;
    function startFirebasePolling() {
        // Logika untuk mendengarkan /scans/latest dari Firebase via API Laravel
        pollInterval = setInterval(async () => {
            try {
                const response = await fetch('/canteen/cashier/poll-scan');
                const data = await response.json();
                if (data.success && data.uid) {
                    processPayment(data.uid, data.device_id);
                }
            } catch (e) {}
        }, 2000);
    }

    function stopFirebasePolling() {
        clearInterval(pollInterval);
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

    async function processPayment(uid, deviceId = null) {
        stopFirebasePolling();
        
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
                    device_id: deviceId,
                    items: cart.map(i => ({ id: i.id, quantity: i.qty }))
                })
            });

            const data = await response.json();
            
            if (data.success) {
                document.getElementById('payment-step-processing').style.display = 'none';
                document.getElementById('payment-step-success').style.display = 'block';
                document.getElementById('success-msg').innerText = `Terima kasih ${data.user_name}!\nSisa Saldo: Rp ${data.new_balance}`;
            } else {
                alert(data.message);
                document.getElementById('payment-step-processing').style.display = 'none';
                document.getElementById('payment-step-scan').style.display = 'block';
                startFirebasePolling();
            }
        } catch (e) {
            alert('Terjadi kesalahan server');
            document.getElementById('payment-step-processing').style.display = 'none';
            document.getElementById('payment-step-scan').style.display = 'block';
        }
    }

    function resetPOS() {
        cart = [];
        renderCart();
        closePaymentModal();
        document.getElementById('payment-step-success').style.display = 'none';
        document.getElementById('payment-step-scan').style.display = 'block';
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

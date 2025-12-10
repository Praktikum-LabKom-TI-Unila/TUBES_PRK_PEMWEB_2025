<div class="flex flex-col md:flex-row gap-6 h-[calc(100vh-140px)]">
  
  <div class="md:w-2/3 flex flex-col">
    <div class="glass-effect rounded-3xl h-full overflow-hidden flex flex-col">
      
      <div class="p-6 pb-4">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold flex items-center gap-2">
                <i data-lucide="grid" class="w-6 h-6"></i> Daftar Menu
            </h2>
        </div>
        
        <div class="relative">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"></i>
            <input type="text" id="search-input" 
                   class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-blue-500/50 transition placeholder-gray-400" 
                   placeholder="Cari menu makanan atau minuman...">
        </div>
      </div>
      
      <div id="product-grid" class="grid grid-cols-2 md:grid-cols-3 gap-4 overflow-y-auto px-6 pb-6 custom-scroll">
        <?php foreach ($products as $p): ?>
          <div onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)" 
               class="product-item bg-white/5 hover:bg-white/10 p-4 rounded-2xl cursor-pointer transition border border-white/10 group"
               data-name="<?= strtolower($p['name']) ?>">
               
            <div class="aspect-square bg-blue-500/20 rounded-xl mb-3 flex items-center justify-center relative overflow-hidden">
              <?php if($p['image'] != 'default.jpg'): ?>
                  <img src="<?= BASE_URL ?>/uploads/products/<?= $p['image'] ?>" class="w-full h-full object-cover" onerror="this.src=''">
              <?php else: ?>
                  <i data-lucide="coffee" class="w-10 h-10 text-blue-200 group-hover:scale-110 transition"></i>
              <?php endif; ?>
            </div>
            
            <h3 class="font-bold text-lg truncate"><?= $p['name'] ?></h3>
            <p class="text-blue-300">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
            <p class="text-xs text-gray-100 mt-1">Stok: <?= $p['stock'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <div class="lg:w-1/3 flex flex-col">
    <div class="glass-effect p-6 rounded-3xl h-full flex flex-col justify-between">
      
      <div class="flex-1 overflow-hidden flex flex-col">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="shopping-cart" class="w-6 h-6"></i> Keranjang
        </h2>
        <div id="cart-items" class="space-y-3 overflow-y-auto pr-2 custom-scroll flex-1">
          <p class="text-gray-400 text-center italic mt-10">Keranjang masih kosong</p>
        </div>
      </div>

      <div class="border-t border-white/20 pt-4 mt-4 space-y-4">
        
        <div class="flex justify-between text-2xl font-bold text-white">
            <span>Total Bayar</span>
            <span id="total-final-display" class="text-blue-300">Rp 0</span>
        </div>

        <div>
            <label class="text-xs text-gray-300 mb-1 block">Metode Pembayaran</label>
            <select id="payment-method" class="w-full bg-white/5 border border-white/10 rounded-xl p-2 text-white outline-none focus:border-blue-500/50">
                <option value="CASH" class="bg-gray-800"> Tunai (Cash)</option>
                <option value="QRIS" class="bg-gray-800"> QRIS (Scan)</option>
                <option value="TRANSFER" class="bg-gray-800"> Transfer Bank</option>
            </select>
        </div>

        <div id="cash-input-area" class="space-y-2">
            <div>
                <label class="text-xs text-gray-300 block">Uang Diterima (Rp)</label>
                <input type="number" id="pay-amount" class="w-full bg-white/5 border border-white/10 rounded-xl p-2 text-white outline-none focus:border-blue-500/50" placeholder="0">
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-400">Kembalian:</span>
                <span id="change-display" class="text-yellow-400 font-bold">Rp 0</span>
            </div>
        </div>

        <div id="qris-area" class="hidden text-center p-4 bg-white rounded-xl">
            <img src="https://upload.wikimedia.org/wikipedia/commons/d/d0/QR_code_for_mobile_English_Wikipedia.svg" class="w-24 h-24 mx-auto opacity-80">
            <p class="text-black font-bold text-sm mt-2">Scan QRIS</p>
            <p class="text-gray-600 text-xs">Otomatis Terverifikasi</p>
        </div>

        <div id="transfer-area" class="hidden text-center p-4 bg-white/10 border border-white/20 rounded-xl">
            <p class="text-gray-300 text-xs mb-1">Silakan transfer ke:</p>
            <p class="text-blue-300 font-bold text-lg">BCA 123-456-7890</p>
            <p class="text-white font-semibold">a.n LokaPOS Owner</p>
            <p class="text-gray-400 text-xs mt-2">*Cek mutasi setelah transfer</p>
        </div>

        <button onclick="processCheckout()" 
                class="btn-primary w-full py-3 rounded-xl font-bold text-lg flex items-center justify-center gap-2 hover:shadow-lg transition">
          <i data-lucide="credit-card" class="w-6 h-6"></i>
          Bayar Sekarang
        </button>

      </div>
    </div>
  </div>
</div>

<style>
  .custom-scroll::-webkit-scrollbar {
    width: 8px;
  }
  .custom-scroll::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
  }
  .custom-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 10px;
  }
  .custom-scroll::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
  }
</style>

<script>
  let cart = [];

  // --- 1. FITUR LIVE SEARCH ---
  document.getElementById('search-input').addEventListener('input', function(e) {
    const keyword = e.target.value.toLowerCase(); // Ambil teks kecil
    const products = document.querySelectorAll('.product-item'); // Ambil semua kartu produk
    
    products.forEach(item => {
        const name = item.getAttribute('data-name'); // Ambil nama produk dari atribut
        // Cek apakah nama produk mengandung keyword
        if (name.includes(keyword)) {
            item.classList.remove('hidden'); // Munculkan
        } else {
            item.classList.add('hidden'); // Sembunyikan
        }
    });
  });

  // --- 2. LOGIKA METODE PEMBAYARAN ---
  document.getElementById('payment-method').addEventListener('change', function(e) {
      const method = e.target.value;
      const cashArea = document.getElementById('cash-input-area');
      const qrisArea = document.getElementById('qris-area');
      const transferArea = document.getElementById('transfer-area');
      const payInput = document.getElementById('pay-amount');
      
      // Hitung total saat ini
      const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

      // Reset: Sembunyikan semua area dulu
      cashArea.classList.add('hidden');
      qrisArea.classList.add('hidden');
      transferArea.classList.add('hidden');

      if (method === 'CASH') {
          cashArea.classList.remove('hidden');
          payInput.value = ''; // Reset input uang
          document.getElementById('change-display').innerText = 'Rp 0';
      } else if (method === 'QRIS') {
          qrisArea.classList.remove('hidden');
          payInput.value = total; // QRIS dianggap uang pas
      } else if (method === 'TRANSFER') {
          transferArea.classList.remove('hidden');
          payInput.value = total; // Transfer dianggap uang pas
      }
  });

  // --- 3. LOGIKA HITUNG KEMBALIAN ---
  document.getElementById('pay-amount').addEventListener('input', function(e) {
      const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
      const pay = parseFloat(e.target.value) || 0;
      const change = pay - total;
      
      const display = document.getElementById('change-display');
      if(change >= 0) {
          display.innerText = 'Rp ' + change.toLocaleString('id-ID');
          display.classList.remove('text-red-400');
          display.classList.add('text-yellow-400');
      } else {
          display.innerText = 'Kurang Rp ' + Math.abs(change).toLocaleString('id-ID');
          display.classList.add('text-red-400');
          display.classList.remove('text-yellow-400');
      }
  });

  // --- FUNGSI UTAMA (Render, Add, Checkout) ---
  
  function addToCart(product) {
    const existing = cart.find(item => item.id == product.id);
    if (existing) {
      if (existing.qty < product.stock) {
        existing.qty++;
      } else {
        alert('Stok habis!'); return;
      }
    } else {
      if (product.stock > 0) {
        cart.push({
          ...product,
          qty: 1
        });
      } else {
        alert('Stok habis!'); return;
      }
    }
    renderCart();
  }

  function renderCart() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('total-final-display');
    
    container.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
      container.innerHTML = '<p class="text-gray-100 text-center italic mt-10">Keranjang masih kosong</p>';
      totalEl.innerText = 'Rp 0';
      document.getElementById('pay-amount').value = ''; 
      return;
    }

    cart.forEach((item, index) => {
      total += item.price * item.qty;
      container.innerHTML += `
        <div class="flex justify-between items-center bg-white/5 p-3 rounded-xl border border-white/10">
          <div>
            <h4 class="font-semibold text-sm">${item.name}</h4>
            <p class="text-xs text-blue-200">Rp ${parseInt(item.price).toLocaleString('id-ID')}</p>
          </div>
          <div class="flex items-center gap-3">
            <button onclick="updateQty(${index}, -1)" class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center hover:bg-red-500/50">-</button>
            <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
            <button onclick="updateQty(${index}, 1)" class="w-6 h-6 bg-white/10 rounded-full flex items-center justify-center hover:bg-green-500/50">+</button>
          </div>
        </div>
      `;
    });

    totalEl.innerText = 'Rp ' + total.toLocaleString('id-ID');
    
    // Auto-update input uang jika mode non-tunai aktif
    const method = document.getElementById('payment-method').value;
    if (method !== 'CASH') {
        document.getElementById('pay-amount').value = total;
    }
  }

  function updateQty(index, change) {
    cart[index].qty += change;
    if (cart[index].qty <= 0) cart.splice(index, 1);
    renderCart();
    document.getElementById('pay-amount').dispatchEvent(new Event('input')); // Recalculate change
  }

  async function processCheckout() {
    if (cart.length === 0) return alert('Keranjang kosong!');
    
    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    const pay = parseFloat(document.getElementById('pay-amount').value) || 0;
    const method = document.getElementById('payment-method').value;

    if (pay < total) return alert('Uang pembayaran kurang!');
    if (!confirm('Proses pembayaran?')) return;
    
    try {
      const response = await fetch('<?= BASE_URL ?>/pos/checkout', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ 
            cart: cart, 
            total_final: total, // Kirim total bersih
            pay_amount: pay,
            change_amount: pay - total,
            payment_method: method
        })
      });

      const result = await response.json();
      
      if (result.status === 'success') {
        alert('Transaksi Berhasil!');
        window.location.href = '<?= BASE_URL ?>/pos/struk/' + result.transId;
      } else {
        alert('Gagal: ' + result.message);
      }
    } catch (error) {
      console.error(error);
      alert('Terjadi kesalahan sistem');
    }
  }
</script>
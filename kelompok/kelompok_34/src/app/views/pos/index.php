<div class="flex flex-col lg:flex-row gap-6 min-h-[calc(100vh-140px)]">

  <div class="lg:w-2/3 flex flex-col">
    <div class="glass-effect p-6 rounded-3xl h-full overflow-hidden flex flex-col">
      <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
        <i data-lucide="grid" class="w-6 h-6"></i> Daftar Menu
      </h2>

      <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto pr-2 custom-scroll">
        <?php foreach ($products as $p): ?>
          <div onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)"
            class="bg-white/5 hover:bg-white/10 p-4 rounded-2xl cursor-pointer transition border border-white/10 group">
            <div class="aspect-square bg-blue-500/20 rounded-xl mb-3 flex items-center justify-center">
              <i data-lucide="coffee" class="w-10 h-10 text-blue-200 group-hover:scale-110 transition"></i>
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

      <div>
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="shopping-cart" class="w-6 h-6"></i> Keranjang
        </h2>
        <div id="cart-items" class="space-y-3 overflow-y-auto max-h-[400px] pr-2 custom-scroll">
          <p class="text-gray-100 text-center italic mt-10">Keranjang masih kosong</p>
        </div>
      </div>

      <div class="border-t border-white/20 pt-4 mt-4">
        <div class="flex justify-between text-xl font-bold mb-4">
          <span>Total:</span>
          <span id="cart-total" class="text-blue-300">Rp 0</span>
        </div>
        <button onclick="processCheckout()"
          class="btn-primary w-full py-4 rounded-xl font-bold text-lg flex items-center justify-center gap-2 hover:shadow-lg transition">
          <i data-lucide="credit-card" class="w-6 h-6"></i>
          Bayar Sekarang
        </button>
      </div>

    </div>
  </div>
</div>

<script>
  let cart = [];

  // Fungsi Tambah Barang
  function addToCart(product) {
    const existing = cart.find(item => item.id == product.id);
    if (existing) {
      if (existing.qty < product.stock) {
        existing.qty++;
      } else {
        alert('Stok habis!');
        return;
      }
    } else {
      if (product.stock > 0) {
        cart.push({
          ...product,
          qty: 1
        });
      } else {
        alert('Stok habis!');
        return;
      }
    }
    renderCart();
  }

  // Fungsi Render Tampilan Keranjang
  function renderCart() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total');

    container.innerHTML = '';
    let total = 0;

    if (cart.length === 0) {
      container.innerHTML = '<p class="text-gray-100 text-center italic mt-10">Keranjang masih kosong</p>';
      totalEl.innerText = 'Rp 0';
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
  }

  // Update Jumlah Item
  function updateQty(index, change) {
    cart[index].qty += change;
    if (cart[index].qty <= 0) {
      cart.splice(index, 1);
    }
    renderCart();
  }

  // Fungsi Checkout ke Backend
  async function processCheckout() {
    if (cart.length === 0) return alert('Keranjang kosong!');
    if (!confirm('Proses pembayaran?')) return;

    const total = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);

    try {
      const response = await fetch('<?= BASE_URL ?>/pos/checkout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          cart: cart,
          total: total
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
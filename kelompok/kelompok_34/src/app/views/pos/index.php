<div class="flex flex-col lg:flex-row gap-6 min-h-[calc(100vh-140px)]">

  <!-- Product List -->
  <div class="lg:w-2/3 flex flex-col">
    <div class="glass-effect rounded-3xl min-h-full overflow-hidden flex flex-col">
      <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-500/40 text-green-200 p-4 mb-6 rounded-xl border border-green-400/60 text-sm flex items-start gap-3">
          <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
          <span><?= $_SESSION['success'] ?></span>
        </div>
      <?php unset($_SESSION['success']);
      endif; ?>

      <div class="p-6 pb-4">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-2xl font-bold flex items-center gap-2">
            <i data-lucide="grid" class="w-6 h-6"></i> Daftar Menu
          </h2>
        </div>
        <div class="relative">
          <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-100"></i>
          <input type="text" id="search-input"
            class="w-full bg-white/5 border border-white/10 rounded-xl py-3 pl-12 pr-4 text-white focus:outline-none focus:border-blue-500/50 transition placeholder-gray-100"
            placeholder="Cari menu...">
        </div>
      </div>

      <div id="product-grid" class="grid grid-cols-2 lg:grid-cols-3 gap-4 overflow-y-auto px-6 pb-6">
        <?php foreach ($products as $p): ?>
          <div onclick="addToCart(<?= htmlspecialchars(json_encode($p)) ?>)"
            class="product-item bg-white/5 hover:bg-white/10 p-4 rounded-2xl cursor-pointer transition border border-white/10"
            data-name="<?= strtolower($p['name']) ?>">
            <div class="aspect-square bg-blue-500/20 rounded-xl mb-3 flex items-center justify-center">
              <?php if ($p['image'] != 'default.jpg'): ?>
                <img src="<?= BASE_URL ?>/public/uploads/products/<?= $p['image'] ?>" class="w-full h-full object-cover rounded-xl" loading="lazy">
              <?php else: ?>
                <i data-lucide="coffee" class="w-10 h-10 text-blue-200"></i>
              <?php endif; ?>
            </div>
            <h3 class="font-bold text-lg truncate"><?= $p['name'] ?></h3>
            <p class="text-gray-100">Rp <?= number_format($p['price'], 0, ',', '.') ?></p>
            <p class="text-xs text-gray-100 mt-1">Stok: <?= $p['stock'] ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

  <!-- Cart -->
  <div class="lg:w-1/3 flex flex-col">
    <div class="glass-effect p-6 rounded-3xl min-h-full flex flex-col justify-between">

      <div class="flex-1 overflow-hidden flex flex-col">
        <h2 class="text-2xl font-bold mb-4 flex items-center gap-2">
          <i data-lucide="shopping-cart" class="w-6 h-6"></i> Keranjang
        </h2>
        <div id="cart-items" class="space-y-3 overflow-y-auto pr-2 flex-1">
          <p class="text-gray-100 text-center italic mt-10">Keranjang masih kosong</p>
        </div>
      </div>

      <div class="border-t border-white/20 pt-4 mt-4 space-y-4">

        <div class="flex justify-between text-2xl font-bold text-white">
          <span>Total Bayar</span>
          <span id="total-final-display" class="text-gray-100">Rp 0</span>
        </div>

        <div>
          <label class="text-xs text-gray-300 mb-1 block">Metode Pembayaran</label>
          <select id="payment-method" class="w-full bg-white/5 border border-white/10 rounded-xl p-2 text-white outline-none focus:border-blue-500/50">
            <option value="CASH" class="bg-gray-800">Tunai (Cash)</option>
            <option value="MIDTRANS" class="bg-gray-800">Midtrans</option>
          </select>
        </div>

        <div id="cash-input-area" class="space-y-2">
          <div>
            <label class="text-xs text-gray-300 block">Uang Diterima (Rp)</label>
            <input type="number" id="pay-amount" class="w-full bg-white/5 border border-white/10 rounded-xl p-2 text-white outline-none focus:border-blue-500/50" placeholder="0">
          </div>
          <div class="flex justify-between text-sm">
            <span class="text-gray-100">Kembalian:</span>
            <span id="change-display" class="text-yellow-400 font-bold">Rp 0</span>
          </div>
        </div>

        <div id="midtrans-area" class="hidden text-center p-4 bg-gradient-to-br from-blue-500/20 to-purple-500/20 border border-blue-400/30 rounded-xl">
          <div class="flex items-center justify-center gap-2 mb-2">
            <i data-lucide="credit-card" class="w-5 h-5 text-blue-300"></i>
            <p class="text-white font-bold">Pembayaran Digital</p>
          </div>
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

<script>
  let cart = [];

  document.addEventListener("DOMContentLoaded", initPOS);

  function initPOS() {
    const searchInput = document.getElementById("search-input");
    if (searchInput) {
      searchInput.addEventListener("input", function(e) {
        const keyword = e.target.value.toLowerCase();
        document.querySelectorAll(".product-item").forEach((item) => {
          const name = item.getAttribute("data-name") || "";
          item.style.display = name.includes(keyword) ? "" : "none";
        });
      });
    }

    const paymentMethod = document.getElementById("payment-method");
    if (paymentMethod) {
      paymentMethod.addEventListener("change", handlePaymentChange);
    }

    const payAmount = document.getElementById("pay-amount");
    if (payAmount) {
      payAmount.addEventListener("input", calculateChange);
    }
  }

  function handlePaymentChange(e) {
    const method = e.target.value;

    const cashArea = document.getElementById("cash-input-area");
    const midtransArea = document.getElementById("midtrans-area");
    const payInput = document.getElementById("pay-amount");

    if (cashArea) cashArea.classList.add("hidden");
    if (midtransArea) midtransArea.classList.add("hidden");

    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);

    if (method === "CASH") {
      if (cashArea) cashArea.classList.remove("hidden");
      if (payInput) payInput.value = "";
      document.getElementById("change-display").innerText = "Rp 0";
    } else if (method === "MIDTRANS") {
      if (midtransArea) midtransArea.classList.remove("hidden");
      if (payInput) payInput.value = total;
    }
  }

  function calculateChange() {
    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const pay = parseFloat(document.getElementById("pay-amount")?.value || 0);
    const change = pay - total;

    const display = document.getElementById("change-display");
    if (!display) return;

    if (change >= 0) {
      display.innerText = "Rp " + change.toLocaleString("id-ID");
      display.className = "text-yellow-400 font-bold";
    } else {
      display.innerText =
        "Kurang Rp " + Math.abs(change).toLocaleString("id-ID");
      display.className = "text-red-400 font-bold";
    }
  }

  function addToCart(product) {
    const existing = cart.find((item) => item.id == product.id);

    if (existing) {
      if (existing.qty < product.stock) {
        existing.qty++;
      } else {
        alert("Stok habis!");
        return;
      }
    } else {
      if (product.stock > 0) {
        cart.push({
          ...product,
          qty: 1
        });
      } else {
        alert("Stok habis!");
        return;
      }
    }

    renderCart();
  }

  function renderCart() {
    const container = document.getElementById("cart-items");
    const totalEl = document.getElementById("total-final-display");
    if (!container || !totalEl) return;

    container.innerHTML = "";
    let total = 0;

    if (cart.length === 0) {
      container.innerHTML =
        '<p class="text-gray-100 text-center italic mt-10">Keranjang masih kosong</p>';
      totalEl.innerText = "Rp 0";
      return;
    }

    cart.forEach((item, index) => {
      total += item.price * item.qty;

      const div = document.createElement("div");
      div.className =
        "flex justify-between items-center bg-white/5 p-3 rounded-xl border border-white/10";

      div.innerHTML = `
      <div>
        <h4 class="font-semibold text-sm">${item.name}</h4>
        <p class="text-xs text-blue-200">Rp ${parseInt(item.price).toLocaleString(
          "id-ID"
        )}</p>
      </div>
      <div class="flex items-center gap-3">
        <button onclick="updateQty(${index}, -1)" class="w-6 h-6 bg-white/10 rounded-full">-</button>
        <span class="text-sm font-bold w-4 text-center">${item.qty}</span>
        <button onclick="updateQty(${index}, 1)" class="w-6 h-6 bg-white/10 rounded-full">+</button>
      </div>
    `;

      container.appendChild(div);
    });

    totalEl.innerText = "Rp " + total.toLocaleString("id-ID");

    const method = document.getElementById("payment-method")?.value;
    if (method !== "CASH") {
      const payInput = document.getElementById("pay-amount");
      if (payInput) payInput.value = total;
    }
  }

  function updateQty(index, change) {
    cart[index].qty += change;
    if (cart[index].qty <= 0) cart.splice(index, 1);

    renderCart();
    calculateChange();
  }

  async function openMidtransSnap(snapToken, transId) {
    snap.pay(snapToken, {
      onSuccess: async () => {
        await fetch("<?= BASE_URL ?>/pos/update-payment-success", {
          method: "POST",
          headers: {
            "Content-Type": "application/json"
          },
          body: JSON.stringify({
            transId
          })
        });

        alert("Pembayaran Midtrans berhasil!");
        window.location.href = "<?= BASE_URL ?>/pos/struk/" + transId;
      },
      onPending: () => {
        alert("Pembayaran sedang diproses...");
        window.location.href = "<?= BASE_URL ?>/pos/struk/" + transId;
      },
      onError: () => {
        alert("Pembayaran gagal!");
      },
      onClose: () => {
        alert("Anda menutup popup pembayaran");
      },
    });
  }

  async function processCheckout() {
    if (cart.length === 0) return alert("Keranjang kosong!");

    const total = cart.reduce((sum, item) => sum + item.price * item.qty, 0);
    const pay = parseFloat(document.getElementById("pay-amount").value || 0);
    const method = document.getElementById("payment-method").value;

    if (method === "CASH" && pay < total) {
      return alert("Uang pembayaran kurang!");
    }

    if (!confirm("Proses pembayaran?")) return;

    const response = await fetch("<?= BASE_URL ?>/pos/checkout", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        cart,
        total_final: total,
        pay_amount: pay,
        change_amount: method === "CASH" ? pay - total : 0,
        payment_method: method,
      }),
    });

    const result = await response.json();

    if (result.status === "success") {
      if (result.payment_type === "midtrans" && result.snap_token) {
        if (!window.snap) {
          const script = document.createElement("script");
          script.src =
            "https://app.sandbox.midtrans.com/snap/snap.js";
          script.setAttribute(
            "data-client-key",
            "<?= MIDTRANS_CLIENT_KEY ?>"
          );
          document.head.appendChild(script);
          script.onload = () => openMidtransSnap(result.snap_token, result.transId);
        } else {
          openMidtransSnap(result.snap_token, result.transId);
        }
      } else {
        alert("Transaksi berhasil!");
        window.location.href = "<?= BASE_URL ?>/pos/struk/" + result.transId;
      }
    }
  }
</script>
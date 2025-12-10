/* ============================================================
   SCHOLARBRIDGE — AJAX_SEARCH.JS FINAL
   Search Result Renderer
============================================================ */

const resultList = document.getElementById("resultList");

// Format angka → Rp xxx.xxx
function rupiah(n) {
  return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Render card search result
function renderResult(list) {
  if (!resultList) return;

  resultList.innerHTML = "";

  list.forEach(t => {
    const col = document.createElement("div");
    col.className = "col-md-4 fade-up";

    col.innerHTML = `
      <div class="result-card">
        <div class="result-photo">${t.nama.charAt(0)}</div>

        <div class="result-name">${t.nama}</div>
        <div class="result-subject">${t.mapel}</div>

        <div class="result-price">Rp ${rupiah(t.harga)}</div>

        <a
          href="../public/detail_tutor.php?nama=${encodeURIComponent(t.nama)}&mapel=${encodeURIComponent(t.mapel)}&harga=${t.harga}&rating=${t.rating}"
          class="result-btn"
        >
          Lihat Detail
        </a>
      </div>
    `;

    resultList.appendChild(col);
  });
}

// -------- Initial Load --------
renderResult(tutorsData);

// -------- Search Filter --------
const srInput = document.getElementById("searchInput");
const srButton = document.getElementById("btnSearch");

if (srInput) {
  srInput.addEventListener("input", function() {
    const q = this.value.toLowerCase();

    const filtered = tutorsData.filter(t =>
      t.nama.toLowerCase().includes(q) ||
      t.mapel.toLowerCase().includes(q)
    );

    renderResult(filtered);
  });
}

if (srButton) {
  srButton.addEventListener("click", function() {
    const q = (srInput.value || "").toLowerCase();

    const filtered = tutorsData.filter(t =>
      t.nama.toLowerCase().includes(q) ||
      t.mapel.toLowerCase().includes(q)
    );

    renderResult(filtered);
  });
}

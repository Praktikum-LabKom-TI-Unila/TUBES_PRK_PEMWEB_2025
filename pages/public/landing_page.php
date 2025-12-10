<?php 
$assetPath = "../../assets/";
include '../../layouts/header.php'; 
?>

<main class="site-main">

  <!-- HERO -->
  <section class="hero-section" role="region" aria-label="Hero">
    <div class="hero-overlay">
      <div class="hero-inner">
        <div class="hero-badge">🏆 Platform #1 di Lampung</div>
        <h1 class="hero-title mt-2">
          Raih Prestasi Akademik<br>
          dengan <span class="accent">Tutor Terbaik</span>
        </h1>
        <p class="hero-sub">
          ScholarBridge menghubungkan siswa SD/SMP/SMA dengan tutor mahasiswa berprestasi. 
          Bimbingan fleksibel, terjangkau, dan berkualitas.
        </p>
      </div>
    </div>
  </section>

  <!-- SEARCH -->
  <div class="search-box" role="search" aria-label="Pencarian tutor">
    <input id="searchInput" type="text" placeholder="Cari mata pelajaran atau nama tutor..." aria-label="Cari tutor">
    <button id="btnSearch" class="search-btn" type="button">Cari Tutor</button>
  </div>

  <!-- KENAPA MEMILIH KAMI -->
  <section class="section-why" aria-label="Kenapa memilih kami">
    <div class="section-title">Kenapa Memilih ScholarBridge?</div>
    <div class="section-desc">Solusi bimbingan belajar yang mengutamakan kualitas pengajar — mahasiswa berprestasi, verifikasi dokumen, dan metode pengajaran adaptif.</div>

    <div class="why-grid">
      <div class="why-card">
        <div class="why-icon">✓</div>
        <div class="why-title">Tutor Terverifikasi</div>
        <div class="why-text">Semua tutor diverifikasi dokumen akademik sebelum aktif mengajar.</div>
      </div>

      <div class="why-card">
        <div class="why-icon">🕒</div>
        <div class="why-title">Jadwal Fleksibel</div>
        <div class="why-text">Sesuaikan waktu belajar — online atau tatap muka.</div>
      </div>

      <div class="why-card">
        <div class="why-icon">💸</div>
        <div class="why-title">Harga Terjangkau</div>
        <div class="why-text">Paket yang ramah kantong untuk siswa SD-SMA.</div>
      </div>

      <div class="why-card">
        <div class="why-icon">⭐</div>
        <div class="why-title">Ulasan Nyata</div>
        <div class="why-text">Sistem rating & review membantu memilih tutor terbaik.</div>
      </div>
    </div>
  </section>

  <!-- KATEGORI POPULER -->
  <section class="section-category" aria-label="Kategori populer">
    <div class="section-title">Kategori Populer</div>
    <div class="category-grid">
      <div class="category-card">
        <div class="category-icon">∑</div>
        <div class="category-name">Matematika</div>
      </div>
      <div class="category-card">
        <div class="category-icon">A</div>
        <div class="category-name">Bahasa Inggris</div>
      </div>
      <div class="category-card">
        <div class="category-icon">🔬</div>
        <div class="category-name">Fisika / Kimia</div>
      </div>
      <div class="category-card">
        <div class="category-icon">📚</div>
        <div class="category-name">Bahasa Indonesia</div>
      </div>
      <div class="category-card">
        <div class="category-icon">🧬</div>
        <div class="category-name">Biologi</div>
      </div>
      <div class="category-card">
        <div class="category-icon">💵</div>
        <div class="category-name">Ekonomi</div>
      </div>
    </div>
  </section>

  <!-- CARA KERJA -->
  <section class="section-steps" aria-label="Cara kerja">
    <div class="section-title">Cara Kerja</div>
    <div class="section-desc">3 langkah sederhana untuk mulai belajar bersama tutor terbaik di Lampung.</div>

    <div class="steps">
      <div class="step-card">
        <div class="step-number">1</div>
        <div class="step-title">Cari & Pilih Tutor</div>
        <div class="step-desc">Gunakan pencarian berdasarkan mata pelajaran atau lokasi.</div>
      </div>

      <div class="step-card">
        <div class="step-number">2</div>
        <div class="step-title">Booking Jadwal</div>
        <div class="step-desc">Ajukan permintaan pertemuan, lalu tutor konfirmasi.</div>
      </div>

      <div class="step-card">
        <div class="step-number">3</div>
        <div class="step-title">Belajar & Beri Ulasan</div>
        <div class="step-desc">Sesi selesai, beri penilaian untuk kualitas tutor.</div>
      </div>
    </div>
  </section>

      <!-- rendered by JS -->
    </div>
  </section>

  <!-- TESTIMONIAL -->
  <section class="section-testimoni" aria-label="Testimoni">
    <div class="section-title">Apa Kata Siswa</div>

    <div class="testi-grid">
      <div class="testi-card">
        <div class="testi-text">"Tutor sangat sabar dan membuat saya paham materi dengan cepat."</div>
        <div class="testi-user">— Nadia</div>
        <div class="testi-rating">⭐ 5.0</div>
      </div>

      <div class="testi-card">
        <div class="testi-text">"Metode pengajaran rapi, persiapan ujian jadi terarah."</div>
        <div class="testi-user">— Raka</div>
        <div class="testi-rating">⭐ 4.8</div>
      </div>

      <div class="testi-card">
        <div class="testi-text">"Harga terjangkau, kualitas tidak murahan."</div>
        <div class="testi-user">— Sinta</div>
        <div class="testi-rating">⭐ 4.7</div>
      </div>
    </div>
  </section>

  <!-- PRICING -->
  <section class="section-pricing" aria-label="Paket harga">
    <div class="section-title">Paket Belajar</div>
    <div class="section-desc">Pilih paket yang sesuai kebutuhan. Paket fleksibel dan transparan.</div>

    <div class="price-grid">
      <div class="price-card">
        <div class="price-tier">Paket Basic</div>
        <div class="price-value">Rp 250.000</div>
        <div class="price-benefit">4 sesi / bulan</div>
        <a href="#" class="price-btn">Pilih Paket</a>
      </div>

      <div class="price-card">
        <div class="price-tier">Paket Reguler</div>
        <div class="price-value">Rp 350.000</div>
        <div class="price-benefit">8 sesi / bulan</div>
        <a href="#" class="price-btn">Pilih Paket</a>
      </div>

      <div class="price-card">
        <div class="price-tier">Paket Intensif</div>
        <div class="price-value">Rp 650.000</div>
        <div class="price-benefit">16 sesi / bulan</div>
        <a href="#" class="price-btn">Pilih Paket</a>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section-faq" aria-label="FAQ">
    <div class="section-title">Pertanyaan Umum</div>
    <div class="section-desc">Pertanyaan yang sering diajukan — jawaban singkat dan jelas.</div>

    <div class="faq-list" style="max-width:900px;margin:24px auto 60px;">
      <div class="faq-item" data-idx="0">
        <div class="faq-question">Bagaimana cara verifikasi tutor?</div>
        <div class="faq-answer">Tutor wajib unggah KTM/transkrip. Admin akan memverifikasi sebelum akun aktif.</div>
      </div>

      <div class="faq-item" data-idx="1">
        <div class="faq-question">Apakah ada jaminan kualitas?</div>
        <div class="faq-answer">Sistem rating & review membantu memastikan kualitas — Anda bisa melihat ulasan siswa sebelumnya.</div>
      </div>

      <div class="faq-item" data-idx="2">
        <div class="faq-question">Bagaimana cara pembayaran?</div>
        <div class="faq-answer">Saat ini diarahkan ke WhatsApp tutor; integrasi payment gateway akan dikembangkan pada backend selanjutnya.</div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="section-cta" aria-label="Call to action">
    <div class="cta-title">Siap mulai belajar dengan tutor berkualitas?</div>
    <div class="cta-sub">Daftar sekarang dan temukan tutor yang cocok untuk kebutuhan akademikmu.</div>
    <a class="cta-btn" href="../auth/form_register.php">Daftar Sekarang</a>
  </section>

</main>

<?php include '../../layouts/footer.php'; ?>

<!-- ======= Inline JS: render tutors + FAQ interaksi (simple) ======= -->
<script>
  // dummy tutors — nanti diganti fetch ke backend
  const tutorsData = [
    { nama: "Rizky Ramadhan", mapel: "Matematika", harga: 350000, rating: 4.9 },
    { nama: "Aulia Putri", mapel: "Bahasa Inggris", harga: 420000, rating: 5.0 },
    { nama: "Dimas Wahyu", mapel: "Fisika", harga: 300000, rating: 4.7 },
    { nama: "Nadia Fitri", mapel: "Kimia", harga: 400000, rating: 4.8 },
    { nama: "Farhan Akbar", mapel: "Biologi", harga: 320000, rating: 4.6 },
    { nama: "Sinta Maharani", mapel: "Bahasa Indonesia", harga: 280000, rating: 4.5 },
    { nama: "Adi Pratama", mapel: "Ekonomi", harga: 330000, rating: 4.7 },
    { nama: "Maya Sari", mapel: "Sejarah", harga: 260000, rating: 4.4 },
  ];

  // format harga
  function rupiah(n){ return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }

  // render tutor cards
  function renderTutors(list){
    const container = document.getElementById('tutorContainer');
    if(!container) return;
    container.innerHTML = '';
    list.forEach(t => {
      const el = document.createElement('div');
      el.className = 'tutor-card';
      el.innerHTML = `
        <div class="tutor-photo" aria-hidden="true">${t.nama.split(' ')[0].charAt(0)}</div>
        <div class="tutor-name">${t.nama}</div>
        <div class="tutor-sub">${t.mapel}</div>
        <div class="tutor-meta">
          <div class="rating">★ ${t.rating}</div>
          <div style="margin-left:auto" class="tutor-price">Rp ${rupiah(t.harga)}</div>
        </div>
        <a href="detail_tutor.php?nama=${encodeURIComponent(t.nama)}&mapel=${encodeURIComponent(t.mapel)}&harga=${t.harga}&rating=${t.rating}" class="btn-detail" role="button" aria-label="Detail ${t.nama}">Detail Tutor</a>
      `;
      container.appendChild(el);
    });
  }

  // initial render
  renderTutors(tutorsData);

  // search realtime (on this page only)
  const searchInput = document.getElementById('searchInput');
  const btnSearch = document.getElementById('btnSearch');
  if(searchInput){
    searchInput.addEventListener('input', function(e){
      const q = e.target.value.toLowerCase().trim();
      const filtered = tutorsData.filter(t => t.nama.toLowerCase().includes(q) || t.mapel.toLowerCase().includes(q));
      renderTutors(filtered);
    });
    // support button search for keyboard-less test
    btnSearch && btnSearch.addEventListener('click', function(){
      const q = (searchInput.value||'').toLowerCase().trim();
      const filtered = tutorsData.filter(t => t.nama.toLowerCase().includes(q) || t.mapel.toLowerCase().includes(q));
      renderTutors(filtered);
    });
  }

  // FAQ accordion simple
  document.querySelectorAll('.faq-item').forEach(item => {
    item.addEventListener('click', function(){
      // toggle active class
      const isActive = this.classList.contains('active');
      document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('active'));
      if(!isActive) this.classList.add('active');
    });
  });

  // simple scroll reveal for elements with .fade-up
  function revealOnScroll(){
    document.querySelectorAll('.fade-up').forEach(el => {
      const rect = el.getBoundingClientRect();
      if(rect.top < window.innerHeight - 80){ el.classList.add('show'); }
    });
  }
  window.addEventListener('scroll', revealOnScroll);
  window.addEventListener('load', revealOnScroll);

</script>

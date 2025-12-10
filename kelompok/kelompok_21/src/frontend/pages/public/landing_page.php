<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once '../../layouts/header.php';
?>

<section style="background-color: var(--color-primary); color: white; padding: 80px 0; text-align: center;">
    <div class="container">
        <h1 style="font-size: 3rem; margin-bottom: 20px;">Raih Prestasi Akademikmu 🚀</h1>
        <p style="font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9;">Temukan kakak tutor mahasiswa berprestasi dari universitas terbaik di Lampung.</p>
        
        <div style="background: white; padding: 10px; border-radius: 50px; max-width: 600px; margin: 0 auto; display: flex; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
            <input type="text" id="keyword" placeholder="Cari mata pelajaran (Matematika, Bahasa Inggris...)" style="border: none; flex: 1; padding: 15px 25px; border-radius: 50px; outline: none; font-size: 1rem;">
            <button onclick="searchTutor()" style="background-color: var(--color-secondary); color: var(--color-text-dark); border: none; padding: 10px 30px; border-radius: 50px; font-weight: bold; cursor: pointer; transition: 0.3s;">Cari</button>
        </div>
    </div>
</section>

<section style="padding: 60px 0; background-color: white;">
    <div class="container" style="display: flex; justify-content: space-around; flex-wrap: wrap; gap: 20px;">
        <div style="text-align: center; max-width: 300px;">
            <h3 style="color: var(--color-primary); margin-bottom: 10px;">🎓 Tutor Terverifikasi</h3>
            <p style="color: var(--color-text-light);">Semua tutor adalah mahasiswa aktif yang telah divalidasi dengan KTM resmi.</p>
        </div>
        <div style="text-align: center; max-width: 300px;">
            <h3 style="color: var(--color-primary); margin-bottom: 10px;">💰 Harga Terjangkau</h3>
            <p style="color: var(--color-text-light);">Biaya les privat yang ramah di kantong pelajar, langsung dari mahasiswa.</p>
        </div>
        <div style="text-align: center; max-width: 300px;">
            <h3 style="color: var(--color-primary); margin-bottom: 10px;">📅 Jadwal Fleksibel</h3>
            <p style="color: var(--color-text-light);">Atur jadwal belajar sesuai kesepakatan kamu dan kakak tutor.</p>
        </div>
    </div>
</section>

<section style="padding: 60px 0;">
    <div class="container">
        <h2 style="text-align: center; margin-bottom: 40px; color: var(--color-text-dark);">Tutor Terbaru</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px;">
            
            <div class="card" style="border-top: 5px solid var(--color-primary); transition: transform 0.3s;">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 80px; height: 80px; background-color: #ddd; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">👨‍🎓</div>
                    <h4 style="margin: 0;">Kak Budi</h4>
                    <span style="font-size: 0.9rem; color: var(--color-text-light);">Pendidikan Matematika - UNILA</span>
                </div>
                <p style="text-align: center; font-size: 0.9rem; margin-bottom: 20px;">"Jago banget ngajarin Aljabar dan Kalkulus dasar."</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border); padding-top: 15px;">
                    <span style="font-weight: bold; color: var(--color-primary);">Rp 40.000/jam</span>
                    <a href="#" style="padding: 8px 15px; background-color: var(--color-primary); color: white; border-radius: 5px; font-size: 0.9rem;">Lihat Profil</a>
                </div>
            </div>

            <div class="card" style="border-top: 5px solid var(--color-secondary);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 80px; height: 80px; background-color: #ddd; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">👩‍🏫</div>
                    <h4 style="margin: 0;">Kak Siti</h4>
                    <span style="font-size: 0.9rem; color: var(--color-text-light);">Sastra Inggris - ITERA</span>
                </div>
                <p style="text-align: center; font-size: 0.9rem; margin-bottom: 20px;">"Conversation & Grammar expert. TOEFL 550+."</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border); padding-top: 15px;">
                    <span style="font-weight: bold; color: var(--color-primary);">Rp 50.000/jam</span>
                    <a href="#" style="padding: 8px 15px; background-color: var(--color-primary); color: white; border-radius: 5px; font-size: 0.9rem;">Lihat Profil</a>
                </div>
            </div>

            <div class="card" style="border-top: 5px solid var(--color-primary);">
                <div style="text-align: center; margin-bottom: 15px;">
                    <div style="width: 80px; height: 80px; background-color: #ddd; border-radius: 50%; margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem;">👨‍💻</div>
                    <h4 style="margin: 0;">Kak Andi</h4>
                    <span style="font-size: 0.9rem; color: var(--color-text-light);">Ilmu Komputer - UNILA</span>
                </div>
                <p style="text-align: center; font-size: 0.9rem; margin-bottom: 20px;">"Belajar Coding Python & Web Design dari nol."</p>
                <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--color-border); padding-top: 15px;">
                    <span style="font-weight: bold; color: var(--color-primary);">Rp 60.000/jam</span>
                    <a href="#" style="padding: 8px 15px; background-color: var(--color-primary); color: white; border-radius: 5px; font-size: 0.9rem;">Lihat Profil</a>
                </div>
            </div>

        </div>
        
        <div style="text-align: center; margin-top: 40px;">
            <a href="#" style="padding: 12px 30px; border: 2px solid var(--color-primary); color: var(--color-primary); border-radius: 50px; font-weight: bold; transition: 0.3s;">Lihat Semua Tutor</a>
        </div>
    </div>
</section>

<?php require_once '../../layouts/footer.php'; ?>
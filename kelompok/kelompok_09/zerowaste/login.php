<?php 
session_start();
include 'includes/header.php';
include 'config/database.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>

<section class="min-h-screen flex items-center bg-gradient-to-br from-green-50 via-white to-slate-100 pt-32 pb-20">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            <!-- ✅ KIRI: TEKS PROMOSI -->
            <div class="hidden lg:block">
                <span class="inline-flex items-center py-1.5 px-3 rounded-full bg-green-100 text-primary text-sm font-bold mb-6 gap-2">
                    <i class="bi bi-lock-fill"></i> Login ZeroWaste
                </span>

                <h1 class="text-4xl md:text-5xl font-extrabold text-dark mb-6 leading-tight">
                    Berbagi Makanan,<br>
                    <span class="text-primary">Kurangi Limbah.</span>
                </h1>

                <p class="text-lg text-slate-600 mb-8 max-w-lg">
                    Masuk ke akun ZeroWaste untuk mulai berbagi makanan berlebih dengan sesama mahasiswa secara gratis, transparan, dan berdampak nyata.
                </p>

                <a href="index.php" class="inline-flex items-center gap-2 text-primary font-semibold hover:underline">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>
            </div>

            <!-- ✅ KANAN: FORM LOGIN -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 lg:p-10 w-full max-w-md mx-auto">

                <div class="text-center mb-8">
                    <div class="w-12 h-12 bg-green-100 text-primary rounded-2xl flex items-center justify-center mx-auto mb-4 text-xl">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-dark">Masuk Akun</h2>
                    <p class="text-slate-600 mt-2">Silakan login untuk melanjutkan</p>
                </div>

                <!-- ✅ FORM LOGIN -->
                <form action="login_process.php" method="POST" class="space-y-5">

                    <div>
                        <label class="block text-sm font-semibold mb-2">Email</label>
                        <input type="email" name="email" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:outline-none"
                            placeholder="example@email.com">
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-sm font-semibold mb-2">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-primary focus:outline-none"
                            placeholder="********">
                    </div>

                    <button type="submit"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 rounded-xl transition-all shadow-lg hover:-translate-y-1">
                        Masuk
                    </button>
                </form>

                <p class="text-center text-slate-600 text-sm mt-6">
                    Belum punya akun?
                    <a href="register.php" class="text-primary font-bold hover:underline">Daftar Sekarang</a>
                </p>

            </div>

        </div>
        
        <!-- Back to Home -->
        <div class="text-center mt-8">
            <a href="index.php" class="text-slate-600 hover:text-primary font-medium flex items-center justify-center gap-2 transition-all">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

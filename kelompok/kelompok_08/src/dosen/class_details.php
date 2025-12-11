<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dosen') header("Location: ../index.php");

$cid = $_GET['id'];
$did = $_SESSION['user']['id'];

$classInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM classes WHERE id='$cid' AND dosen_id='$did'"));
if(!$classInfo) header("Location: dashboard.php");

if(isset($_GET['kick'])){
    $sid = $_GET['kick'];
    mysqli_query($conn, "DELETE FROM enrollments WHERE class_id='$cid' AND student_id='$sid'");
    echo "<script>window.location='class_details.php?id=$cid';</script>";
}

if(isset($_GET['delete_class'])){
  
    mysqli_query($conn, "DELETE FROM classes WHERE id='$cid' AND dosen_id='$did'");
    
    echo "<script>alert('Kelas ".$classInfo['class_name']." berhasil dihapus!'); window.location='dashboard.php';</script>";
    exit;
}

$totalMhs = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM enrollments WHERE class_id='$cid'"))['c'];
$totalQuiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM quizzes WHERE class_id='$cid'"))['c'];

echo getHeader("Kelas: " . $classInfo['class_name']);
echo getNavbar("dosen");
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <main class="flex-1 container mx-auto px-8 py-8 max-w-8xl relative">
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 animate-fade-in">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-indigo-50 text-primary rounded-xl flex items-center justify-center text-3xl">📂</div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900"><?= $classInfo['class_name'] ?></h1>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm text-gray-500">Kode Kelas:</span>
                        <span class="font-mono bg-gray-100 text-gray-700 px-2 py-0.5 rounded font-bold tracking-wider select-all border border-gray-200"><?= $classInfo['class_code'] ?></span>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-4 text-sm items-center">
                <div class="text-center px-4 border-r border-gray-100">
                    <span class="block font-bold text-gray-800 text-lg"><?= $totalMhs ?></span>
                    <span class="text-gray-400 text-xs uppercase">Mahasiswa</span>
                </div>
                <div class="text-center px-4 border-r border-gray-100">
                    <span class="block font-bold text-gray-800 text-lg"><?= $totalQuiz ?></span>
                    <span class="text-gray-400 text-xs uppercase">Kuis</span>
                </div>
                <a href="?id=<?= $cid ?>&delete_class=1" 
                   onclick="return confirm('ANDA YAKIN INGIN MENGHAPUS KELAS INI? SEMUA DATA MAHASISWA, KUIS, SOAL, DAN NILAI AKAN HILANG PERMANEN!')"
                   class="px-3 py-2 bg-red-100 text-red-600 rounded-lg font-bold text-xs hover:bg-red-600 hover:text-white transition shadow-sm whitespace-nowrap">
                   Hapus Kelas
                </a>
            </div>
            </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Daftar Mahasiswa</div>
                <div class="max-h-[500px] overflow-y-auto divide-y">
                    <?php
                    $mhsQuery = mysqli_query($conn, "SELECT u.id, u.name, u.email FROM enrollments e JOIN users u ON e.student_id = u.id WHERE e.class_id = '$cid' ORDER BY u.name ASC");
                    if(mysqli_num_rows($mhsQuery) == 0): ?>
                        <div class="p-6 text-center text-sm text-gray-400">Belum ada mahasiswa.</div>
                    <?php else:
                    while($m = mysqli_fetch_assoc($mhsQuery)): ?>
                    <div class="px-6 py-4 flex justify-between items-center hover:bg-gray-50 group transition">
                        <div>
                            <p class="text-sm font-bold text-gray-800"><?= $m['name'] ?></p>
                            <p class="text-xs text-gray-400"><?= $m['email'] ?></p>
                        </div>
                        <a href="?id=<?= $cid ?>&kick=<?= $m['id'] ?>" onclick="return confirm('Keluarkan siswa ini?')" class="opacity-0 group-hover:opacity-100 text-xs text-red-500 hover:text-red-700 font-bold border border-red-200 px-2 py-1 rounded hover:bg-red-50 transition">Kick</a>
                    </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-bold text-gray-800 text-lg flex items-center gap-2"><span class="bg-primary w-1 h-6 rounded"></span> Daftar Kuis</h3>
                </div>

                <div class="grid gap-4">
                    <?php 
                    $qs = mysqli_query($conn, "SELECT *, (SELECT COUNT(*) FROM questions WHERE quiz_id=quizzes.id) as q_count FROM quizzes WHERE class_id='$cid' ORDER BY created_at DESC");
                    if(mysqli_num_rows($qs) == 0): ?>
                        <div class="bg-white p-10 rounded-xl border-2 border-dashed border-gray-200 text-center text-gray-400">
                            <span class="text-4xl block mb-2">📝</span>
                            Belum ada kuis.<br>Klik tombol (+) untuk membuat.
                        </div>
                    <?php else:
                    while($q = mysqli_fetch_assoc($qs)): ?>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 group">
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800 text-lg group-hover:text-primary transition"><?= $q['title'] ?></h4>
                            <p class="text-sm text-gray-500 mt-1 line-clamp-1"><?= $q['description'] ? $q['description'] : 'Tidak ada deskripsi.' ?></p>
                            <div class="flex gap-3 mt-3 text-xs font-medium text-gray-400">
                                <span class="bg-gray-100 px-2 py-1 rounded">❓ <?= $q['q_count'] ?> Soal</span>
                                <span>📅 <?= date('d M Y', strtotime($q['created_at'])) ?></span>
                            </div>
                        </div>
                        
                        <a href="quiz_details.php?quiz_id=<?= $q['id'] ?>" class="flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition font-bold text-sm shadow-sm whitespace-nowrap">
                            Detail & Nilai
                        </a>
                    </div>
                    <?php endwhile; endif; ?>
                </div>
            </div>

        </div>
    </main>

    <a href="create_quiz.php?class_id=<?= $cid ?>" class="fixed bottom-20 right-10 bg-primary hover:bg-primaryDark text-white w-14 h-14 rounded-full shadow-lg shadow-indigo-500/30 flex items-center justify-center transition-all duration-300 hover:scale-110 hover:-translate-y-1 z-50 group" title="Buat Kuis Baru">
        <span class="text-3xl font-light leading-none mb-1">+</span>
    </a>

    <?= getFooter() ?>
</body></html>
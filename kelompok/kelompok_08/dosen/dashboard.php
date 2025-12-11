<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dosen') header("Location: ../index.php");

if(isset($_POST['create'])) {
    $n = $_POST['name']; $c = strtoupper(substr(md5(time()),0,6)); $d = $_SESSION['user']['id'];
    mysqli_query($conn, "INSERT INTO classes (dosen_id, class_name, class_code) VALUES ('$d', '$n', '$c')");
    header("Location: dashboard.php");
}

if(isset($_POST['edit_class'])) {
    $id = $_POST['id']; 
    $n = $_POST['name']; 
    $d = $_SESSION['user']['id'];
    mysqli_query($conn, "UPDATE classes SET class_name='$n' WHERE id='$id' AND dosen_id='$d'");
    header("Location: dashboard.php");
}

if(isset($_GET['del'])) {
    $id = $_GET['del']; $d = $_SESSION['user']['id'];
    mysqli_query($conn, "DELETE FROM classes WHERE id='$id' AND dosen_id='$d'");
    header("Location: dashboard.php");
}

echo getHeader("Dashboard Dosen"); echo getNavbar("dosen");
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
<main class="flex-1 container mx-auto px-8 py-8 max-w-8xl">
<div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg mb-8">
            <div>
                <h2 class="text-xl font-bold">Buat Kelas Baru</h2>
                <p class="text-sm opacity-90">Buat ruang belajar baru untuk mahasiswa.</p>
            </div>
            
            <form method="POST" class="flex w-full md:w-auto gap-2">
                <input type="text" name="name" placeholder="Ex: Struktur Data A" 
                       class="px-4 py-2 rounded-lg text-black font-mono w-full md:w-64 outline-none shadow-inner text-sm placeholder-gray-500" required>
                <button type="submit" name="create" 
                        class="px-5 py-2 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 shadow transition transform active:scale-95 text-sm whitespace-nowrap">
                    Buat
                </button>
            </form>
        </div>

        <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2"><span class="w-1 h-6 bg-primary rounded"></span> Daftar Kelas Aktif</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $did = $_SESSION['user']['id'];
            $cls = mysqli_query($conn, "SELECT c.*, (SELECT COUNT(*) FROM enrollments e WHERE e.class_id=c.id) as tot FROM classes c WHERE c.dosen_id='$did' ORDER BY c.id DESC");
            if(mysqli_num_rows($cls)==0) echo "<div class='col-span-full py-8 text-center text-gray-400 bg-white rounded border border-dashed'>Belum ada kelas.</div>";
            while($r=mysqli_fetch_assoc($cls)): ?>
            <div class="bg-white rounded-xl shadow-sm hover:shadow-lg transition border border-gray-100 group relative flex flex-col">
                
                <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition flex gap-2">
                    <button onclick="openEditModal(this)" 
                            data-id="<?= $r['id'] ?>" 
                            data-name="<?= htmlspecialchars($r['class_name']) ?>" 
                            data-code="<?= $r['class_code'] ?>"
                            class="p-2 bg-yellow-100 text-yellow-600 rounded hover:bg-yellow-200 transition" title="Edit">
                        ✏️
                    </button>
                    
                    <a href="?del=<?= $r['id'] ?>" onclick="return confirm('Hapus kelas ini permanen?')" class="p-2 bg-red-100 text-red-600 rounded hover:bg-red-200" title="Hapus">🗑️</a>
                </div>

                <div class="p-6 flex-1">
                    <div class="flex justify-between mb-2"><span class="bg-indigo-50 text-indigo-700 px-2 py-1 rounded text-xs font-bold">KELAS</span><span class="text-sm text-gray-500">👥 <?= $r['tot'] ?></span></div>
                    <h4 class="text-xl font-bold text-gray-900"><?= $r['class_name'] ?></h4>
                    <p class="text-sm text-gray-500 mt-1">Kode: <span class="font-mono bg-gray-100 px-2 rounded font-bold text-gray-800 select-all"><?= $r['class_code'] ?></span></p>
                </div>
                <div class="p-4 bg-gray-50 border-t rounded-b-xl"><a href="class_details.php?id=<?= $r['id'] ?>" class="block w-full text-white text-center py-2 bg-primary border rounded font-medium hover:bg-primaryDark transition">Masuk & Kelola</a></div>
            </div>
            <?php endwhile; ?>
        </div>
    </main>

    <div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="toggleModal(false)"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-fade-in border border-gray-100">
                    <form method="POST">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-bold leading-6 text-gray-900">Edit Kelas</h3>
                                <button type="button" onclick="toggleModal(false)" class="text-gray-400 hover:text-red-500">✕</button>
                            </div>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Kelas</label>
                                    <input type="text" name="name" id="edit_name" class="w-full px-4 py-2 border rounded-lg focus:ring-1 focus:ring-primary outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Kelas (Read-only)</label>
                                    <input type="text" id="edit_code" class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500 font-mono tracking-widest cursor-not-allowed" readonly>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit" name="edit_class" class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark sm:ml-3 sm:w-auto transition">Simpan Perubahan</button>
                            <button type="button" onclick="toggleModal(false)" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?= getFooter() ?>

    <script>
        function toggleModal(show) {
            const modal = document.getElementById('editModal');
            if (show) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; 
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; 
            }
        }

        function openEditModal(btn) {
            document.getElementById('edit_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_name').value = btn.getAttribute('data-name');
            document.getElementById('edit_code').value = btn.getAttribute('data-code');
            
            toggleModal(true);
        }
    </script>
</body></html>
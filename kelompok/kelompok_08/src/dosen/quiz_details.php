<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dosen') header("Location: ../index.php");

$qid = $_GET['quiz_id'] ?? null;
if(!$qid) header("Location: dashboard.php");

$quiz = mysqli_fetch_assoc(mysqli_query($conn, "SELECT q.*, c.class_name, c.id as class_id FROM quizzes q JOIN classes c ON q.class_id = c.id WHERE q.id='$qid'"));
if(!$quiz) header("Location: dashboard.php");

if(isset($_GET['del_q'])){
    $dqid = $_GET['del_q']; mysqli_query($conn, "DELETE FROM questions WHERE id='$dqid'");
    header("Location: quiz_details.php?quiz_id=$qid");
}

if(isset($_POST['add_q'])){
    $q = mysqli_real_escape_string($conn, $_POST['question']);
    $oa=$_POST['oa']; $ob=$_POST['ob']; $oc=$_POST['oc']; $od=$_POST['od']; $ans=$_POST['ans'];
    mysqli_query($conn, "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) VALUES ('$qid', '$q', '$oa', '$ob', '$oc', '$od', '$ans')");
    echo "<script>window.location='quiz_details.php?quiz_id=$qid';</script>";
}

if(isset($_POST['update_q'])){
    $id = $_POST['qid']; 
    $q = mysqli_real_escape_string($conn, $_POST['question']);
    $oa=$_POST['oa']; $ob=$_POST['ob']; $oc=$_POST['oc']; $od=$_POST['od']; $ans=$_POST['ans'];
    
    mysqli_query($conn, "UPDATE questions SET question_text='$q', option_a='$oa', option_b='$ob', option_c='$oc', option_d='$od', correct_answer='$ans' WHERE id='$id'");
    echo "<script>window.location='quiz_details.php?quiz_id=$qid';</script>";
}

$stats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as submission_count, AVG(score) as avg_score, MAX(score) as max_score FROM quiz_results WHERE quiz_id='$qid'"));

echo getHeader("Detail: " . $quiz['title']);
echo getNavbar("dosen");
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    
    <main class="flex-1 container mx-auto px-8 py-8 max-w-8xl">
        
        <div class="mb-8 border-b pb-4">
            <div class="flex justify-between items-end">
                <div>
                    <span class="text-xs font-bold text-indigo-500 uppercase tracking-widest">Detail Kuis</span>
                    <h1 class="text-3xl font-bold text-gray-900 mt-1"><?= $quiz['title'] ?></h1>
                    <p class="text-gray-500 text-sm mt-1">Kelas: <span class="font-medium text-gray-700"><?= $quiz['class_name'] ?></span></p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <div class="bg-white p-5 rounded-xl shadow-sm border border-l-4 border-l-blue-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Mahasiswa Submit</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['submission_count'] ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-l-4 border-l-green-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Rata-rata Nilai</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= number_format($stats['avg_score'], 1) ?></p>
            </div>
            <div class="bg-white p-5 rounded-xl shadow-sm border border-l-4 border-l-purple-500">
                <p class="text-xs font-bold text-gray-400 uppercase">Nilai Tertinggi</p>
                <p class="text-2xl font-bold text-gray-800 mt-1"><?= $stats['max_score'] ?? '-' ?></p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden h-fit">
                <div class="px-6 py-4 border-b bg-gray-50 font-bold text-gray-700">Hasil Pengerjaan</div>
                <div class="max-h-[600px] overflow-y-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 border-b">
                            <tr><th class="px-6 py-3">Nama</th><th class="px-6 py-3 text-center">Waktu</th><th class="px-6 py-3 text-right">Nilai</th></tr>
                        </thead>
                        <tbody class="divide-y">
                            <?php
                            $results = mysqli_query($conn, "SELECT qr.*, u.name, u.email FROM quiz_results qr JOIN users u ON qr.student_id = u.id WHERE qr.quiz_id='$qid' ORDER BY qr.score DESC");
                            if(mysqli_num_rows($results) == 0): echo "<tr><td colspan='3' class='p-6 text-center text-gray-400 italic'>Belum ada data.</td></tr>";
                            else: while($r = mysqli_fetch_assoc($results)): $color = ($r['score'] >= 60) ? 'text-green-600' : 'text-red-500'; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800"><?= $r['name'] ?></p>
                                    <p class="text-xs text-gray-400"><?= $r['email'] ?></p>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-gray-500"><?= date('d M H:i', strtotime($r['submitted_at'])) ?></td>
                                <td class="px-6 py-4 text-right font-bold text-lg <?= $color ?>"><?= $r['score'] ?></td>
                            </tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-3 border-b bg-gray-50 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <h3 class="font-bold text-gray-700">Daftar Pertanyaan</h3>
                            <?php $qCount = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM questions WHERE quiz_id='$qid'"))['c']; ?>
                            <span class="text-xs bg-gray-200 px-2 py-0.5 rounded font-bold"><?= $qCount ?></span>
                        </div>
                        
                        <button onclick="toggleModal('addModal', true)" class="bg-primary hover:bg-primaryDark text-white text-xs font-bold px-3 py-2 rounded-lg shadow transition flex items-center gap-1">
                            <span>+</span> Tambah Soal
                        </button>
                    </div>

                    <div class="max-h-[600px] overflow-y-auto divide-y bg-white">
                        <?php
                        $questions = mysqli_query($conn, "SELECT * FROM questions WHERE quiz_id='$qid' ORDER BY id ASC");
                        if(mysqli_num_rows($questions) == 0) echo "<div class='p-10 text-center text-gray-400 italic flex flex-col items-center'><span class='text-3xl mb-2'>📝</span>Belum ada soal dibuat.<br>Klik tombol Tambah Soal.</div>";
                        $no = 1;
                        while($q = mysqli_fetch_assoc($questions)): ?>
                        <div class="p-5 hover:bg-gray-50 group relative transition border-l-4 border-transparent hover:border-primary">
                            <div class="absolute top-4 right-4 hidden group-hover:flex gap-2">
                                <button onclick="openEditModal(this)" 
                                        data-id="<?= $q['id'] ?>"
                                        data-q="<?= htmlspecialchars($q['question_text']) ?>"
                                        data-a="<?= htmlspecialchars($q['option_a']) ?>"
                                        data-b="<?= htmlspecialchars($q['option_b']) ?>"
                                        data-c="<?= htmlspecialchars($q['option_c']) ?>"
                                        data-d="<?= htmlspecialchars($q['option_d']) ?>"
                                        data-ans="<?= $q['correct_answer'] ?>"
                                        class="p-1.5 bg-yellow-100 text-yellow-700 rounded hover:bg-yellow-200 transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                
                                <a href="?quiz_id=<?= $qid ?>&del_q=<?= $q['id'] ?>" onclick="return confirm('Hapus?')" class="p-1.5 bg-red-100 text-red-700 rounded hover:bg-red-200 transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </a>
                            </div>

                            <div class="flex gap-3 mb-2 pr-12"> 
                                <span class="flex-shrink-0 w-6 h-6 bg-indigo-100 text-indigo-700 rounded-full flex items-center justify-center text-xs font-bold"><?= $no++ ?></span>
                                <p class="font-bold text-gray-800 text-sm"><?= nl2br($q['question_text']) ?></p>
                            </div>
                            <div class="ml-9 grid grid-cols-2 gap-x-4 text-xs text-gray-600">
                                <div class="<?= $q['correct_answer']=='a'?'text-green-600 font-bold':'' ?>">A. <?= $q['option_a'] ?></div>
                                <div class="<?= $q['correct_answer']=='b'?'text-green-600 font-bold':'' ?>">B. <?= $q['option_b'] ?></div>
                                <div class="<?= $q['correct_answer']=='c'?'text-green-600 font-bold':'' ?>">C. <?= $q['option_c'] ?></div>
                                <div class="<?= $q['correct_answer']=='d'?'text-green-600 font-bold':'' ?>">D. <?= $q['option_d'] ?></div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div id="addModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="toggleModal('addModal', false)"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-fade-in border border-gray-100">
                    <form method="POST">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-4"><h3 class="text-lg font-bold leading-6 text-gray-900">Tambah Soal</h3><button type="button" onclick="toggleModal('addModal', false)" class="text-gray-400 hover:text-red-500">✕</button></div>
                            <div class="space-y-4">
                                <textarea name="question" class="w-full px-4 py-2 border rounded-lg focus:ring-1 focus:ring-primary outline-none text-sm" rows="3" placeholder="Tulis pertanyaan..." required></textarea>
                                <div class="grid grid-cols-1 gap-3">
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">A</span><input name="oa" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">B</span><input name="ob" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">C</span><input name="oc" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">D</span><input name="od" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                </div>
                                <div class="flex items-center justify-between"><label class="text-xs font-bold text-gray-500 uppercase">Kunci</label><select name="ans" class="px-3 py-1 border rounded text-sm font-bold text-primary outline-none"><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option></select></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t"><button type="submit" name="add_q" class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark sm:ml-3 sm:w-auto">Simpan</button><button type="button" onclick="toggleModal('addModal', false)" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" onclick="toggleModal('editModal', false)"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-fade-in border border-gray-100">
                    <form method="POST">
                        <input type="hidden" name="qid" id="edit_qid">
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-center mb-4"><h3 class="text-lg font-bold leading-6 text-gray-900">Edit Soal</h3><button type="button" onclick="toggleModal('editModal', false)" class="text-gray-400 hover:text-red-500">✕</button></div>
                            <div class="space-y-4">
                                <textarea name="question" id="edit_question" class="w-full px-4 py-2 border rounded-lg focus:ring-1 focus:ring-primary outline-none text-sm" rows="3" required></textarea>
                                <div class="grid grid-cols-1 gap-3">
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">A</span><input name="oa" id="edit_oa" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">B</span><input name="ob" id="edit_ob" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">C</span><input name="oc" id="edit_oc" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                    <div class="flex items-center border rounded overflow-hidden"><span class="bg-gray-50 px-3 py-2 text-xs font-bold border-r">D</span><input name="od" id="edit_od" class="w-full px-3 py-2 text-sm outline-none" required></div>
                                </div>
                                <div class="flex items-center justify-between"><label class="text-xs font-bold text-gray-500 uppercase">Kunci</label><select name="ans" id="edit_ans" class="px-3 py-1 border rounded text-sm font-bold text-primary outline-none"><option value="a">A</option><option value="b">B</option><option value="c">C</option><option value="d">D</option></select></div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t"><button type="submit" name="update_q" class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primaryDark sm:ml-3 sm:w-auto">Simpan Perubahan</button><button type="button" onclick="toggleModal('editModal', false)" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button></div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?= getFooter() ?>

    <script>
        function toggleModal(modalID, show) {
            const modal = document.getElementById(modalID);
            if (show) {
                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden'; 
            } else {
                modal.classList.add('hidden');
                document.body.style.overflow = 'auto'; 
            }
        }

        function openEditModal(btn) {
            document.getElementById('edit_qid').value = btn.getAttribute('data-id');
            document.getElementById('edit_question').value = btn.getAttribute('data-q');
            document.getElementById('edit_oa').value = btn.getAttribute('data-a');
            document.getElementById('edit_ob').value = btn.getAttribute('data-b');
            document.getElementById('edit_oc').value = btn.getAttribute('data-c');
            document.getElementById('edit_od').value = btn.getAttribute('data-d');
            document.getElementById('edit_ans').value = btn.getAttribute('data-ans');
            
            toggleModal('editModal', true);
        }
    </script>
</body></html>
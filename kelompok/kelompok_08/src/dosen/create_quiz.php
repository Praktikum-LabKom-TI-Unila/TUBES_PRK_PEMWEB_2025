<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dosen') header("Location: ../index.php");

$cid = $_GET['class_id'] ?? null;
if(!$cid) header("Location: dashboard.php");

$classInfo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM classes WHERE id='$cid' AND dosen_id='{$_SESSION['user']['id']}'"));
if(!$classInfo) header("Location: dashboard.php");

if (isset($_POST['save_all'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    
    $queryQuiz = "INSERT INTO quizzes (class_id, title, description) VALUES ('$cid', '$title', '$desc')";
    if (mysqli_query($conn, $queryQuiz)) {
        $quiz_id = mysqli_insert_id($conn);
        
        if (isset($_POST['question']) && is_array($_POST['question'])) {
            $questions = $_POST['question'];
            $opt_a = $_POST['oa'];
            $opt_b = $_POST['ob'];
            $opt_c = $_POST['oc'];
            $opt_d = $_POST['od'];
            $answers = $_POST['ans'];

            for ($i = 0; $i < count($questions); $i++) {
                $q_text = mysqli_real_escape_string($conn, $questions[$i]);
                $oa = mysqli_real_escape_string($conn, $opt_a[$i]);
                $ob = mysqli_real_escape_string($conn, $opt_b[$i]);
                $oc = mysqli_real_escape_string($conn, $opt_c[$i]);
                $od = mysqli_real_escape_string($conn, $opt_d[$i]);
                $ans = mysqli_real_escape_string($conn, $answers[$i]);

                $sqlQ = "INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer) 
                         VALUES ('$quiz_id', '$q_text', '$oa', '$ob', '$oc', '$od', '$ans')";
                mysqli_query($conn, $sqlQ);
            }
        }
        
        echo "<script>alert('Kuis dan Soal berhasil disimpan!'); window.location='class_details.php?id=$cid';</script>";
    }
}

echo getHeader("Buat Kuis & Soal");
echo getNavbar("dosen");
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-4xl">
        <form method="POST" id="quizForm">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-primary"></div>
                <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <span class="bg-indigo-100 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                    Informasi Dasar
                </h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Judul Kuis</label>
                        <input type="text" name="title" placeholder="Contoh: Kuis Harian - Stack" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">Deskripsi / Instruksi</label>
                        <textarea name="description" placeholder="Kerjakan dengan teliti..." class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-primary outline-none" rows="2"></textarea>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                        <span class="bg-indigo-100 text-primary w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                        Daftar Pertanyaan
                    </h2>
                    <button type="button" onclick="addQuestion()" class="text-sm bg-white border border-primary text-primary px-4 py-2 rounded-lg font-bold hover:bg-indigo-50 transition shadow-sm">
                        + Tambah Soal
                    </button>
                </div>

                <div id="questionsContainer" class="space-y-6">
                    </div>
                
                <div id="emptyState" class="p-8 text-center border-2 border-dashed border-gray-300 rounded-xl text-gray-400 hidden">
                    Belum ada soal ditambahkan. Klik tombol "+ Tambah Soal" di atas.
                </div>
            </div>

            <div class="sticky bottom-4 z-40 mt-10">
                <button type="submit" name="save_all" class="w-full bg-primary text-white py-4 rounded-xl font-bold text-lg shadow-lg hover:bg-primaryDark transition transform active:scale-[0.99] flex items-center justify-center gap-2">
                  <p>Simpan Soal</p>
                </button>
            </div>

        </form>
    </main>

    <?= getFooter() ?>

    <script>
        let questionCount = 0;

        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questionsContainer');
            
            const html = `
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 relative animate-fade-in group">
                <div class="flex justify-between items-start mb-4">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest bg-gray-50 px-2 py-1 rounded">Soal #${questionCount}</span>
                    <button type="button" onclick="this.closest('.group').remove()" class="text-gray-400 hover:text-red-500 transition" title="Hapus Soal">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>

                <div class="mb-4">
                    <textarea name="question[]" class="w-full px-4 py-3 border rounded-lg focus:ring-1 focus:ring-primary outline-none bg-gray-50 focus:bg-white transition text-gray-800" rows="2" placeholder="Tulis pertanyaan disini..." required></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-1 ring-primary transition">
                        <span class="bg-gray-100 w-10 py-2 text-center font-bold text-gray-500 text-sm border-r">A</span>
                        <input type="text" name="oa[]" class="w-full px-3 py-2 outline-none text-sm" placeholder="Pilihan A" required>
                    </div>
                    <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-1 ring-primary transition">
                        <span class="bg-gray-100 w-10 py-2 text-center font-bold text-gray-500 text-sm border-r">B</span>
                        <input type="text" name="ob[]" class="w-full px-3 py-2 outline-none text-sm" placeholder="Pilihan B" required>
                    </div>
                    <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-1 ring-primary transition">
                        <span class="bg-gray-100 w-10 py-2 text-center font-bold text-gray-500 text-sm border-r">C</span>
                        <input type="text" name="oc[]" class="w-full px-3 py-2 outline-none text-sm" placeholder="Pilihan C" required>
                    </div>
                    <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-1 ring-primary transition">
                        <span class="bg-gray-100 w-10 py-2 text-center font-bold text-gray-500 text-sm border-r">D</span>
                        <input type="text" name="od[]" class="w-full px-3 py-2 outline-none text-sm" placeholder="Pilihan D" required>
                    </div>
                </div>

                <div class="flex items-center gap-3 bg-green-50 p-3 rounded-lg border border-green-100 w-fit">
                    <label class="text-xs font-bold text-green-700 uppercase">Kunci Jawaban:</label>
                    <select name="ans[]" class="bg-white border border-green-200 text-green-700 text-sm font-bold rounded px-2 py-1 outline-none cursor-pointer">
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                        <option value="d">D</option>
                    </select>
                </div>
            </div>`;

            const div = document.createElement('div');
            div.innerHTML = html;
            container.appendChild(div.firstElementChild);
            div.lastElementChild.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        document.addEventListener("DOMContentLoaded", function() {
            addQuestion();
        });
    </script>
</body>
</html>
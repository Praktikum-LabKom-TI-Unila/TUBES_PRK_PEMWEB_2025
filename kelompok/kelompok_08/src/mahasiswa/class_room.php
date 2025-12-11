<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'mahasiswa') header("Location: ../index.php");
$cid = $_GET['id']; $sid = $_SESSION['user']['id'];
if(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM enrollments WHERE student_id='$sid' AND class_id='$cid'"))==0) header("Location: dashboard.php");
$c = mysqli_fetch_assoc(mysqli_query($conn, "SELECT c.*, u.name as dosen FROM classes c JOIN users u ON c.dosen_id=u.id WHERE c.id='$cid'"));
echo getHeader($c['class_name']); echo getNavbar("mahasiswa");
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-8 py-8 max-w-8xl">
        <div class="mb-8 border-b pb-4 flex justify-between items-end">
            <div><span class="text-xs font-bold text-primary bg-indigo-50 px-2 py-1 rounded">RUANG KELAS</span><h1 class="text-3xl font-bold mt-2 text-gray-900"><?= $c['class_name'] ?></h1><p class="text-gray-500 text-sm mt-1">Dosen: <?= $c['dosen'] ?></p></div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            <div class="lg:col-span-2 space-y-4">
                <h2 class="font-bold text-xl text-gray-800 mb-4">📝 Tugas & Kuis</h2>
                <?php $qs=mysqli_query($conn,"SELECT q.*,(SELECT score FROM quiz_results qr WHERE qr.quiz_id=q.id AND qr.student_id='$sid') as sc FROM quizzes q WHERE q.class_id='$cid' ORDER BY q.id DESC");
                if(mysqli_num_rows($qs)==0) echo "<div class='bg-white p-10 text-center border border-dashed rounded-xl text-gray-400'>Belum ada tugas.</div>";
                while($q=mysqli_fetch_assoc($qs)): $done=$q['sc']!==null; ?>
                <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition flex flex-col md:flex-row justify-between gap-4">
                    <div class="flex-1"><h3 class="font-bold text-lg text-gray-800"><?= $q['title'] ?></h3><p class="text-sm text-gray-500 mt-1"><?= $q['description']?:'Kerjakan dengan teliti.' ?></p></div>
                    <div class="flex items-center">
                        <?php if($done): ?><div class="text-right"><span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded">SELESAI</span><div class="text-2xl font-bold text-green-600 mt-1"><?= $q['sc'] ?></div></div>
                        <?php else: ?><a href="take_quiz.php?id=<?=$q['id']?>" class="px-5 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primaryDark shadow">Kerjakan</a><?php endif; ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="lg:col-span-1 sticky top-24">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b font-bold text-gray-700 flex items-center gap-2"><span>🏆</span> Peringkat Kelas</div>
                    <div class="divide-y"><?php $rs=mysqli_query($conn,"SELECT u.name,u.id,AVG(qr.score) as av FROM enrollments e JOIN users u ON e.student_id=u.id LEFT JOIN quizzes q ON q.class_id=e.class_id LEFT JOIN quiz_results qr ON qr.quiz_id=q.id AND qr.student_id=u.id WHERE e.class_id='$cid' GROUP BY u.id ORDER BY av DESC LIMIT 5");
                    $n=1; while($r=mysqli_fetch_assoc($rs)): $me=$r['id']==$sid; ?>
                    <div class="p-3 flex justify-between items-center <?= $me?'bg-indigo-50':'' ?>"><div class="flex items-center gap-3"><span class="w-6 h-6 rounded-full flex items-center justify-center font-bold text-xs <?= $n==1?'bg-yellow-100 text-yellow-700':'bg-gray-100 text-gray-500' ?>"><?= $n++ ?></span><span class="text-sm font-medium line-clamp-1"><?= $r['name'] ?></span></div><span class="font-bold text-sm text-gray-600"><?= number_format($r['av'],0) ?></span></div>
                    <?php endwhile; ?></div>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
</body></html>
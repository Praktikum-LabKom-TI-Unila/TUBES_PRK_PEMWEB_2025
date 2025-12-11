<?php
session_start(); include '../config.php';
$sid=$_SESSION['user']['id']; $qid=$_GET['id'];

$qCheck=mysqli_query($conn,"SELECT class_id FROM quizzes WHERE id='$qid'"); 
if(mysqli_num_rows($qCheck)==0) header("Location: dashboard.php");
$cid=mysqli_fetch_assoc($qCheck)['class_id'];
if(mysqli_num_rows(mysqli_query($conn,"SELECT * FROM enrollments WHERE student_id='$sid' AND class_id='$cid'"))==0) header("Location: dashboard.php");

if(isset($_POST['sub'])) {
    $sc=0; $qs=mysqli_query($conn,"SELECT * FROM questions WHERE quiz_id='$qid'"); $tot=mysqli_num_rows($qs);
    while($q=mysqli_fetch_assoc($qs)) if($_POST['ans'][$q['id']]==$q['correct_answer']) $sc++;
    $fin=round(($sc/$tot)*100);
    mysqli_query($conn,"INSERT INTO quiz_results (quiz_id,student_id,score) VALUES ('$qid','$sid','$fin')");
    echo "<script>alert('Nilai: $fin'); window.location='class_room.php?id=$cid';</script>";
}
$quiz=mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM quizzes WHERE id='$qid'"));
echo getHeader($quiz['title']);
?>
<body class="bg-gray-50 pb-20 flex flex-col min-h-screen">
    <div class="bg-white p-4 shadow-sm sticky top-0 z-50"><div class="container mx-auto font-bold text-primary text-lg"><?= $quiz['title'] ?></div></div>
    <main class="flex-1 container mx-auto px-4 py-8 max-w-3xl">
        <form method="POST">
            <?php $qs=mysqli_query($conn,"SELECT * FROM questions WHERE quiz_id='$qid'"); $n=1; while($q=mysqli_fetch_assoc($qs)): ?>
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6">
                <p class="font-bold text-gray-800 mb-4 flex gap-3"><span class="w-6 h-6 bg-blue-100 text-blue-700 rounded-full flex flex-shrink-0 items-center justify-center text-xs"><?= $n++ ?></span> <?= nl2br($q['question_text']) ?></p>
                <div class="space-y-2 ml-9"><?php foreach(['a','b','c','d'] as $o): ?>
                    <label class="flex gap-3 p-3 border rounded hover:bg-gray-50 cursor-pointer"><input type="radio" name="ans[<?=$q['id']?>]" value="<?=$o?>" required class="mt-1 accent-primary"> <span class="text-sm"><?=$q['option_'.$o]?></span></label>
                <?php endforeach; ?></div>
            </div>
            <?php endwhile; ?>
            <div class="fixed bottom-0 left-0 w-full bg-white p-4 text-center border-t shadow-lg"><button name="sub" class="px-8 py-3 bg-primary text-white font-bold rounded-lg shadow hover:bg-primaryDark" onclick="return confirm('Kumpul?')">Kirim Jawaban</button></div>
        </form>
    </main>
</body></html>
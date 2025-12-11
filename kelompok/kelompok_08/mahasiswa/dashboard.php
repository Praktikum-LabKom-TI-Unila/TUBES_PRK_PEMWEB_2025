<?php
session_start(); include '../config.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'mahasiswa') header("Location: ../index.php");
$sid = $_SESSION['user']['id'];

if(isset($_POST['join'])) {
    $c = $_POST['code']; $cls = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM classes WHERE class_code='$c'"));
    if($cls) {
        $cid = $cls['id'];
        if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM enrollments WHERE student_id='$sid' AND class_id='$cid'"))==0) {
            mysqli_query($conn, "INSERT INTO enrollments (student_id, class_id) VALUES ('$sid', '$cid')");
            echo "<script>alert('Berhasil!');</script>";
        } else echo "<script>alert('Sudah join!');</script>";
    } else echo "<script>alert('Kode salah!');</script>";
}
echo getHeader("Lobby"); echo getNavbar("mahasiswa");
?>
<body class="bg-gray-100 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-8 py-8 max-w-8xl">
        
        <div class="bg-gradient-to-r bg-indigo-600 rounded-2xl p-6 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg mb-8">
            <div>
                <h2 class="text-xl font-bold">Gabung Kelas</h2>
                <p class="text-sm opacity-90">Masukkan kode unik dari dosen.</p>
            </div>
            <form method="POST" class="flex w-full md:w-auto gap-2">
                <input name="code" placeholder="KODE KELAS" class="px-4 py-2 rounded-lg text-black font-mono font-bold w-full md:w-48 outline-none shadow-inner text-sm" required>
                <button name="join" class="px-5 py-2 bg-white text-primary font-bold rounded-lg hover:bg-gray-100 shadow transition transform active:scale-95 text-sm">Gabung</button>
            </form>
        </div>
        
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-1 h-6 bg-accent rounded"></span> Visualisasi Data
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-10">
            <?php 
            $vs = [
                ['Stack',       '🥞', '../visuals/stack.php',      'blue',   'LIFO Concept'],
                ['Queue',       '🚶', '../visuals/queue.php',      'green',  'FIFO Concept'],
                ['Linked List', '🔗', '../visuals/linkedlist.php', 'purple', 'Nodes & Pointers'],
                ['Array',       '🗃️', '../visuals/array.php',      'indigo', 'Indexing & CRUD'],
                ['Sorting',     '📊', '../visuals/sorting.php',    'red',    'Bubble Sort'],
                ['Searching',   '🔍', '../visuals/searching.php',  'yellow', 'Linear vs Binary']
            ]; 
            foreach($vs as $v): ?>
            <a href="<?=$v[2]?>" class="group bg-white p-4 rounded-xl shadow-sm hover:shadow-md transition-all border border-gray-100 hover:border-transparent flex items-center gap-4">
                <div class="w-12 h-12 bg-<?=$v[3]?>-50 text-<?=$v[3]?>-600 border border-<?=$v[3]?>-100 rounded-lg flex-shrink-0 flex items-center justify-center text-xl group-hover:bg-<?=$v[3]?>-600 group-hover:text-white transition duration-300">
                    <?=$v[1]?>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 group-hover:text-primary transition"><?=$v[0]?></h4>
                    <p class="text-xs text-gray-500 mt-0.5"><?=$v[4]?></p>
                </div>
                <div class="ml-auto text-gray-300 group-hover:text-primary transition group-hover:translate-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span class="w-1 h-6 bg-primary rounded"></span> Kelas Saya
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php 
            $cs = mysqli_query($conn,"SELECT c.*, u.name as dosen, (SELECT COUNT(*) FROM enrollments WHERE class_id=c.id) as tot FROM enrollments e JOIN classes c ON e.class_id=c.id JOIN users u ON c.dosen_id=u.id WHERE e.student_id='$sid' ORDER BY e.id DESC");
            
            if(mysqli_num_rows($cs)==0) echo "<div class='col-span-full py-8 text-center bg-white rounded-xl border border-dashed border-gray-200 text-gray-400 text-sm'>Belum bergabung di kelas manapun.</div>";
            
            while($r=mysqli_fetch_assoc($cs)): ?>
            <a href="class_room.php?id=<?=$r['id']?>" class="bg-white rounded-xl shadow-sm hover:shadow-lg transition overflow-hidden group flex flex-col border border-gray-100">
                <div class="h-20 bg-secondary p-4 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-white opacity-5 rounded-full"></div>
                    <h4 class="font-bold text-white text-base relative z-10 leading-tight"><?=$r['class_name']?></h4>
                    <p class="text-xs text-gray-400 mt-1 relative z-10">Dosen: <?=$r['dosen']?></p>
                </div>
                <div class="p-4 flex justify-between bg-white flex-1 items-center">
                    <span class="text-xs font-bold text-gray-500 bg-gray-100 px-2 py-1 rounded">👥 <?=$r['tot']?> Mahasiswa</span>
                    <span class="text-xs font-bold text-primary group-hover:translate-x-1 transition">Masuk</span>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </main>
    <?= getFooter() ?>
</body></html>
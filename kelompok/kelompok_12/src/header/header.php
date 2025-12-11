<?php
$hari_indo = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
$bulan_indo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hari_ini = isset($hari_indo[date('l')]) ? $hari_indo[date('l')] : date('l');
$tgl_ini = date('d') . ' ' . $bulan_indo[(int)date('m')] . ' ' . date('Y');
$tanggal_lengkap = "$hari_ini, $tgl_ini";

$u_name = isset($_SESSION['user']['full_name']) ? $_SESSION['user']['full_name'] : 'Guest';
$u_role = isset($_SESSION['user']['role']) ? $_SESSION['user']['role'] : 'GUEST';
$u_initial = strtoupper(substr($u_name, 0, 1));
?>

<header class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-sm transition-all md:ml-64 no-print">
    <div class="px-6 py-3 flex justify-between items-center h-16">
        
        <div class="flex items-center gap-4">
            <button id="mobile-menu-btn" class="md:hidden text-gray-500 hover:text-green-600 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                </svg>
            </button>
            <h1 class="text-xl font-bold text-gray-800 tracking-tight flex items-center gap-2">
                <span class="md:hidden text-green-600">NPC</span> 
                <span class="hidden md:block text-gray-700">Sistem Operasional</span>
            </h1>
        </div>

        <div class="flex items-center gap-5">
            <div class="hidden lg:flex flex-col items-end border-r pr-5 border-gray-200">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hari Ini</span>
                <span class="text-sm font-medium text-gray-600"><?php echo $tanggal_lengkap; ?></span>
            </div>

            <div class="hidden md:flex flex-col items-end">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Login Sebagai</span>
                <span class="text-xs font-bold text-green-700 bg-green-100 px-2 py-0.5 rounded border border-green-200 uppercase">
                    <?php echo $u_role; ?>
                </span>
            </div>

            <div class="relative group">
                <a href="../profile/profile.php" class="flex items-center gap-3 pl-2 cursor-pointer">
                    <div class="text-right hidden sm:block leading-tight">
                        <div class="text-sm font-bold text-gray-800 group-hover:text-green-700 transition">
                            <?php echo htmlspecialchars($u_name); ?>
                        </div>
                        <div class="text-xs text-gray-500 lowercase"><?php echo $u_role; ?></div>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-lg shadow-md ring-2 ring-white group-hover:ring-green-200 group-hover:bg-green-700 transition">
                        <?php echo $u_initial; ?>
                    </div>
                </a>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('mobile-menu-btn');
    const sidebar = document.querySelector('aside'); 
    
    if(btn && sidebar) {
        btn.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
            sidebar.classList.toggle('fixed'); 
            sidebar.classList.toggle('inset-y-0');
            sidebar.classList.toggle('left-0');
            sidebar.classList.toggle('z-40');
            sidebar.classList.toggle('w-64');
            sidebar.classList.toggle('bg-slate-900');
        });
    }
});
</script>

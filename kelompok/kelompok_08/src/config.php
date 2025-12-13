<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "coding_interactive";
$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) die("Koneksi Database Gagal: " . mysqli_connect_error());

function getHeader($title) {
    return '
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>'.$title.'</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
          tailwind.config = {
            theme: {
              extend: {
                fontFamily: { sans: ["Inter", "sans-serif"] },
                colors: {
                  primary: "#4F46E5",
                  primaryDark: "#4338ca",
                  secondary: "#1E293B",
                  accent: "#3b82f6"
                },
                animation: { "fade-in": "fadeIn 0.5s ease-out" },
                keyframes: { fadeIn: { "0%": { opacity: "0", transform: "translateY(5px)" }, "100%": { opacity: "1", transform: "translateY(0)" } } }
              }
            }
          }
        </script>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>body { font-family: "Inter", sans-serif; }</style>
    </head>';
}

function getNavbar($section) {
    if(!isset($_SESSION['user'])) return '';
    
    $role = $_SESSION['user']['role'];
    $name = $_SESSION['user']['name'];
    $homeLink = ($role == 'dosen') ? '../dosen/dashboard.php' : '../mahasiswa/dashboard.php';
    $navLinks = '';
    
    $activeHome = ($section == 'mahasiswa' || $section == 'dosen') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary font-medium';
    $navLinks .= '<a href="'.$homeLink.'" class="'.$activeHome.' transition">Home</a>';

    if($role == 'mahasiswa') {
        $activeVis = ($section == 'visuals') ? 'text-primary font-bold' : 'text-gray-600 hover:text-primary font-medium';
        $navLinks .= '
        <div class="relative group">
            <button class="'.$activeVis.' transition flex items-center gap-1">Visualisasi <span>▾</span></button>
            <div class="absolute top-full right-0 mt-2 w-48 bg-white shadow-xl rounded-xl border border-gray-100 p-2 hidden group-hover:block transition-all z-50 animate-fade-in">
                <a href="../visuals/stack.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Stack</a>
                <a href="../visuals/queue.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Queue</a>
                <a href="../visuals/linkedlist.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Linked List</a>
                <a href="../visuals/array.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Array</a>
                <a href="../visuals/sorting.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Sorting</a>
                <a href="../visuals/searching.php" class="block px-4 py-2 text-sm text-gray-600 hover:bg-indigo-50 hover:text-primary rounded-lg transition">Searching</a>
            </div>
        </div>';
    }

    $logoutLink = '<a href="../logout.php" class="px-5 py-2 bg-red-50 text-red-600 rounded-lg font-bold text-sm hover:bg-red-600 hover:text-white transition shadow-sm border border-red-100">Logout</a>';

    return '
    <nav class="sticky top-0 z-50 w-full bg-white/95 backdrop-blur-md border-b border-gray-200 shadow-sm">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                
                <div class="flex items-center gap-4">
                    <a href="'.$homeLink.'" class="flex items-center gap-2 group">
                        <span class="text-primary font-bold text-xl text-gray-800 tracking-tight hidden sm:block">Codevis</span></span>
                    </a>

                    <div class="h-6 w-px bg-gray-300 mx-2 hidden sm:block"></div>

                    <div class="flex flex-col">
                        <span class="text-sm font-bold text-gray-700 leading-none">'.$name.'</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider leading-none mt-1">'.strtoupper($role).'</span>
                    </div>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    '.$navLinks.'
                    '.$logoutLink.'
                </div>

                <div class="md:hidden flex items-center">
                    <button onclick="document.getElementById(\'mobile-menu\').classList.toggle(\'hidden\')" class="text-gray-600 hover:text-primary focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 p-4 space-y-3 shadow-lg">
            <div class="flex flex-col gap-4">
                '.$navLinks.'
                '.$logoutLink.'
            </div>
        </div>
    </nav>';
}

function getFooter() {
    return '
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="container mx-auto px-4 py-6 text-center text-sm text-gray-500">
            &copy; ' . date('Y') . ' <span class="font-bold text-primary">Codevis</span>. All rights reserved.
        </div>
    </footer>';
}
?>
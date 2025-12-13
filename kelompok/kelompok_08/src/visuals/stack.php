<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Stack"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><span class="text-3xl">🥞</span> Stack (Tumpukan)</h1>
                <p class="text-gray-500 text-sm mt-1">Last In, First Out (LIFO)</p>
            </div>
            <div class="flex gap-2 text-xs font-bold text-white">
                <span class="px-3 py-1 bg-blue-500 rounded-full shadow-sm">Push: O(1)</span>
                <span class="px-3 py-1 bg-red-500 rounded-full shadow-sm">Pop: O(1)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 min-h-[500px] flex flex-col items-center justify-center relative overflow-hidden">
                    <div class="absolute top-4 left-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Canvas</div>
                    <div id="stackContainer" class="w-64 h-96 border-x-8 border-b-8 border-slate-300 bg-slate-50 rounded-b-xl shadow-inner flex flex-col-reverse p-4 gap-2 relative transition-all">
                        <div class="absolute -bottom-10 w-full text-center text-xs font-bold text-slate-400 tracking-[0.2em]">BOTTOM</div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-wrap gap-4 items-center justify-center">
                    <input type="number" id="val" value="10" class="w-24 px-4 py-3 border rounded-xl text-center font-bold text-lg outline-none focus:ring-2 focus:ring-primary shadow-inner">
                    <button onclick="push()" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryDark transition shadow-lg transform active:scale-95 flex items-center gap-2">Push</button>
                    <button onclick="pop()" class="px-8 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition shadow-lg transform active:scale-95 flex items-center gap-2">Pop</button>
                    <button onclick="clearStack()" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Reset</button>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                        <h3 class="font-bold text-indigo-900 text-sm uppercase flex items-center gap-2">📚 Konsep Dasar</h3>
                    </div>
                    <div class="p-6 text-sm text-gray-600 space-y-4">
                        <p><strong>Definisi:</strong> Stack adalah struktur data linear yang mengikuti prinsip <span class="font-bold text-indigo-600">LIFO (Last In First Out)</span>. Elemen yang terakhir masuk adalah yang pertama keluar.</p>
                        
                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 text-yellow-800 text-xs">
                            <strong>Analogi:</strong> Tumpukan piring kotor. Anda menaruh piring kotor di paling atas, dan saat mencuci, Anda mengambil piring paling atas juga.
                        </div>

                        <div>
                            <p class="font-bold text-gray-700 mb-1">Operasi Utama:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li><strong>Push:</strong> Menambah item ke posisi paling atas (Top).</li>
                                <li><strong>Pop:</strong> Menghapus item dari posisi paling atas.</li>
                                <li><strong>Peek:</strong> Melihat item teratas tanpa menghapus.</li>
                            </ul>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto relative group">
                            <span class="absolute top-2 right-2 text-[10px] text-gray-500 font-mono">Pseudocode</span>
                            <code class="text-xs font-mono text-green-400 block">
                                function push(data) {<br>
                                &nbsp; top++;<br>
                                &nbsp; stack[top] = data;<br>
                                }<br><br>
                                function pop() {<br>
                                &nbsp; item = stack[top];<br>
                                &nbsp; top--;<br>
                                &nbsp; return item;<br>
                                }
                            </code>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[250px]">
                    <div class="bg-gray-50 px-6 py-3 border-b font-bold text-gray-700 text-sm uppercase">Log Aktivitas</div>
                    <ul id="logs" class="flex-1 overflow-y-auto p-4 space-y-2 text-xs font-mono"></ul>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
    <script>
        const con = document.getElementById('stackContainer'); const logList = document.getElementById('logs');
        function log(msg, type){ const c=type=='push'?'text-green-600 bg-green-50':(type=='pop'?'text-red-600 bg-red-50':'text-gray-600'); logList.insertAdjacentHTML('afterbegin',`<li class="${c} border px-2 py-1.5 rounded mb-1 animate-fade-in flex justify-between"><span>${msg}</span><span class="opacity-50 text-[10px]">${new Date().toLocaleTimeString('id-ID',{hour12:false})}</span></li>`); }
        function push(){ let v=document.getElementById('val').value; if(con.childElementCount>=7)return alert('Full'); let e=document.createElement('div'); e.className="w-full h-14 bg-gradient-to-r from-primary to-indigo-500 text-white flex items-center justify-center font-bold text-xl rounded-lg shadow-md transition-all duration-500 transform translate-y-[-100px] opacity-0 border border-white/20"; e.innerText=v; con.appendChild(e); setTimeout(()=>e.classList.remove('translate-y-[-100px]','opacity-0'),50); log('Push: '+v,'push'); document.getElementById('val').value=Math.floor(Math.random()*100); }
        function pop(){ if(con.childElementCount==0 || con.lastElementChild.classList.contains('absolute'))return alert('Empty'); let e=con.lastElementChild; e.classList.add('opacity-0','scale-50','translate-x-full'); setTimeout(()=>e.remove(),400); log('Pop: '+e.innerText,'pop'); }
        function clearStack(){ while(con.lastElementChild && !con.lastElementChild.classList.contains('absolute'))con.lastElementChild.remove(); log('Reset','neutral'); }
    </script>
</body></html>
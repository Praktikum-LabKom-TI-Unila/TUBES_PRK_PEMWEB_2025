<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Queue"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><span class="text-3xl">🚶</span> Queue (Antrian)</h1>
                <p class="text-gray-500 text-sm mt-1">First In, First Out (FIFO)</p>
            </div>
            <div class="flex gap-2 text-xs font-bold text-white">
                <span class="px-3 py-1 bg-green-500 rounded-full shadow-sm">Enqueue: O(1)</span>
                <span class="px-3 py-1 bg-red-500 rounded-full shadow-sm">Dequeue: O(1)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 min-h-[400px] flex flex-col items-center justify-center overflow-hidden">
                    <div id="qCon" class="h-32 w-full max-w-2xl border-y-4 border-slate-300 bg-slate-50 relative flex items-center px-16 gap-4 overflow-hidden shadow-inner rounded-lg">
                        <div class="absolute left-0 top-0 bottom-0 w-12 bg-red-50 border-r border-red-200 flex items-center justify-center z-10"><span class="text-[10px] font-bold text-red-400 -rotate-90 tracking-widest uppercase">Front</span></div>
                        <div class="absolute right-0 top-0 bottom-0 w-12 bg-green-50 border-l border-green-200 flex items-center justify-center z-10"><span class="text-[10px] font-bold text-green-400 rotate-90 tracking-widest uppercase">Rear</span></div>
                    </div>
                    <p class="text-xs text-gray-400 mt-4">Data masuk dari kanan (Rear), keluar dari kiri (Front)</p>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-wrap gap-4 items-center justify-center">
                    <input type="number" id="val" value="5" class="w-24 px-4 py-3 border rounded-xl text-center font-bold text-lg outline-none focus:ring-2 focus:ring-primary shadow-inner">
                    <button onclick="enq()" class="px-6 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition shadow-lg transform active:scale-95">Enqueue</button>
                    <button onclick="deq()" class="px-6 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition shadow-lg transform active:scale-95">Dequeue</button>
                    <button onclick="reset()" class="px-6 py-3 bg-gray-100 text-gray-600 font-bold rounded-xl hover:bg-gray-200 transition">Reset</button>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                        <h3 class="font-bold text-indigo-900 text-sm uppercase flex items-center gap-2">Konsep Dasar</h3>
                    </div>
                    <div class="p-6 text-sm text-gray-600 space-y-4">
                        <p><strong>Definisi:</strong> Queue adalah struktur data linear yang mengikuti prinsip <span class="font-bold text-indigo-600">FIFO (First In First Out)</span>. Elemen yang pertama kali masuk akan pertama kali dilayani.</p>
                        
                        <div class="bg-green-50 p-3 rounded-lg border border-green-100 text-green-800 text-xs">
                            <strong>Analogi:</strong> Antrian membeli tiket bioskop. Orang yang datang pertama berdiri di paling depan (Front) dan dilayani duluan. Orang baru datang berdiri di belakang (Rear).
                        </div>

                        <div>
                            <p class="font-bold text-gray-700 mb-1">Operasi Utama:</p>
                            <ul class="list-disc pl-5 space-y-1">
                                <li><strong>Enqueue:</strong> Menambah item ke posisi belakang (Rear).</li>
                                <li><strong>Dequeue:</strong> Menghapus item dari posisi depan (Front).</li>
                            </ul>
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto relative group">
                            <span class="absolute top-2 right-2 text-[10px] text-gray-500 font-mono">Pseudocode</span>
                            <code class="text-xs font-mono text-green-400 block">
                                function enqueue(item) {<br>
                                &nbsp; rear++;<br>
                                &nbsp; queue[rear] = item;<br>
                                }<br><br>
                                function dequeue() {<br>
                                &nbsp; item = queue[front];<br>
                                &nbsp; front++;<br>
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
        const con = document.getElementById('qCon'); const logList = document.getElementById('logs');
        function log(m, t){ const c=t=='enq'?'text-green-600 bg-green-50':(t=='deq'?'text-red-600 bg-red-50':'text-gray-600'); logList.insertAdjacentHTML('afterbegin',`<li class="${c} border px-2 py-1 rounded mb-1 animate-fade-in text-xs">${m}</li>`);}
        function enq(){ let v=document.getElementById('val').value; if(con.querySelectorAll('.item').length>=6)return alert('Full'); let e=document.createElement('div'); e.className="item w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 text-white flex-shrink-0 flex items-center justify-center font-bold text-xl rounded-xl shadow-lg transition-all duration-500 transform translate-x-[100px] opacity-0 border-2 border-white z-0"; e.innerText=v; con.appendChild(e); setTimeout(()=>e.classList.remove('translate-x-[100px]','opacity-0'),50); log('Enqueue: '+v,'enq'); document.getElementById('val').value=Math.floor(Math.random()*50); }
        function deq(){ let i=con.querySelectorAll('.item'); if(i.length===0)return alert('Empty'); let e=i[0]; e.classList.add('opacity-0','-translate-x-full','scale-50'); setTimeout(()=>e.remove(),400); log('Dequeue: '+e.innerText,'deq'); }
        function reset(){ con.querySelectorAll('.item').forEach(e=>e.remove()); log('Reset','neutral'); }
    </script>
</body></html>
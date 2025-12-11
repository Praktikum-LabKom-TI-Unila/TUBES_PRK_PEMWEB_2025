<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Array"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><span class="text-3xl">🗃️</span> Array</h1>
                <p class="text-gray-500 text-sm mt-1">Struktur data statis dengan memori berurutan.</p>
            </div>
            <div class="flex gap-2 text-xs font-bold text-white">
                <span class="px-3 py-1 bg-green-500 rounded-full shadow-sm">Access: O(1)</span>
                <span class="px-3 py-1 bg-yellow-500 rounded-full shadow-sm">Search: O(n)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 min-h-[400px] flex flex-col items-center justify-center overflow-x-auto">
                    <div id="arrayCon" class="flex gap-2"></div>
                    <div class="flex gap-2 mt-2 text-xs font-mono text-gray-400" id="indices"></div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-wrap gap-4 items-center justify-center">
                        <div class="flex flex-col"><label class="text-[10px] font-bold text-gray-400 uppercase">Index (0-9)</label><input type="number" id="idx" value="0" min="0" max="9" class="w-20 px-3 py-2 border rounded-lg text-center font-bold outline-none focus:ring-2 focus:ring-primary"></div>
                        <div class="flex flex-col"><label class="text-[10px] font-bold text-gray-400 uppercase">Value</label><input type="number" id="val" value="99" class="w-20 px-3 py-2 border rounded-lg text-center font-bold outline-none focus:ring-2 focus:ring-primary"></div>
                        <div class="w-px h-10 bg-gray-200 mx-2 hidden md:block"></div>
                        <button onclick="insert()" class="px-4 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 transition shadow">Insert/Update</button>
                        <button onclick="del()" class="px-4 py-2 bg-red-500 text-white font-bold rounded-lg hover:bg-red-600 transition shadow">Delete</button>
                        <button onclick="access()" class="px-4 py-2 bg-green-500 text-white font-bold rounded-lg hover:bg-green-600 transition shadow">Access</button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                        <h3 class="font-bold text-indigo-900 text-sm uppercase flex items-center gap-2">📚 Memori Kontigu</h3>
                    </div>
                    <div class="p-6 text-sm text-gray-600 space-y-4">
                        <p>Array menyimpan elemen di lokasi memori yang <strong>bersebelahan (contiguous)</strong>. Ini memungkinkan akses instan menggunakan indeks matematik.</p>
                        
                        <div class="bg-indigo-50 p-3 rounded-lg border border-indigo-100 text-indigo-800 text-xs">
                            <strong>Rumus Alamat:</strong><br>
                            Address(i) = Base + (i * SizeOfElement)
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto relative group">
                            <span class="absolute top-2 right-2 text-[10px] text-gray-500 font-mono">Kode</span>
                            <code class="text-xs font-mono text-blue-300 block">
                                // Akses langsung O(1)<br>
                                arr[i] = value;<br>
                                print(arr[i]);
                            </code>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[250px]">
                    <div class="bg-gray-50 px-6 py-3 border-b font-bold text-gray-700 text-sm uppercase">Log Operasi</div>
                    <ul id="logs" class="flex-1 overflow-y-auto p-4 space-y-2 text-xs font-mono"></ul>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
    <script>
        let arr=[10,25,40,0,0,0,0,0,0,0]; const con=document.getElementById('arrayCon'); const idxCon=document.getElementById('indices'); const logList=document.getElementById('logs');
        function render(){ con.innerHTML=''; idxCon.innerHTML=''; arr.forEach((val,i)=>{ let isEmpty=val===0; let d=document.createElement('div'); d.className=`w-12 h-12 md:w-16 md:h-16 border-2 flex items-center justify-center font-bold text-lg md:text-xl rounded transition-all duration-300 ${isEmpty?'border-gray-200 text-gray-300 bg-gray-50':'border-indigo-500 text-indigo-700 bg-white shadow-sm'}`; d.id=`box-${i}`; d.innerText=isEmpty?'':val; con.appendChild(d); let ix=document.createElement('div'); ix.className="w-12 md:w-16 text-center"; ix.innerText=i; idxCon.appendChild(ix); }); }
        function log(m){ logList.insertAdjacentHTML('afterbegin',`<li class="border-b border-gray-100 pb-1 text-gray-600">> ${m}</li>`); }
        async function highlight(i,t='access'){ let el=document.getElementById(`box-${i}`); let oc=el.className; let ac=t=='access'?'bg-green-100 border-green-500 scale-110':(t=='del'?'bg-red-100 border-red-500':'bg-yellow-100 border-yellow-500'); el.className=`w-12 h-12 md:w-16 md:h-16 border-2 flex items-center justify-center font-bold text-lg md:text-xl rounded transition-all duration-300 ${ac} shadow-lg z-10`; await new Promise(r=>setTimeout(r,600)); render(); }
        function insert(){ let i=parseInt(document.getElementById('idx').value); let v=parseInt(document.getElementById('val').value); if(i<0||i>9)return alert('Index invalid'); arr[i]=v; render(); highlight(i,'insert'); log(`Set arr[${i}] = ${v}`); }
        function del(){ let i=parseInt(document.getElementById('idx').value); if(i<0||i>9)return alert('Index invalid'); arr[i]=0; highlight(i,'del'); setTimeout(render,600); log(`Deleted index ${i}`); }
        function access(){ let i=parseInt(document.getElementById('idx').value); if(i<0||i>9)return alert('Index invalid'); highlight(i,'access'); log(`Accessed arr[${i}]: ${arr[i]}`); }
        render();
    </script>
</body></html>
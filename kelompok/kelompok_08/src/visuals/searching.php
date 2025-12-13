<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Searching"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        <div class="mb-6"><h1 class="text-2xl font-bold text-gray-900">🔍 Searching Algorithms</h1><p class="text-gray-500 text-sm">Linear Search vs Binary Search.</p></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 flex flex-col items-center justify-center gap-6">
                    <div id="searchCon" class="flex flex-wrap justify-center gap-2"></div>
                    <div id="status" class="text-lg font-bold text-gray-800 h-8"></div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-wrap justify-center gap-4 items-center">
                    <button onclick="genSorted()" class="px-4 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg text-sm">Reset (Sorted)</button>
                    <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
                    <input type="number" id="target" value="42" class="w-20 px-3 py-2 border rounded-lg text-center font-bold" placeholder="Cari">
                    <button onclick="linearSearch()" class="px-6 py-2 bg-indigo-600 text-white font-bold rounded-lg hover:bg-indigo-700 shadow">Linear Searching</button>
                    <button onclick="binarySearch()" class="px-6 py-2 bg-purple-600 text-white font-bold rounded-lg hover:bg-purple-700 shadow">Binary Searching</button>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-sm text-gray-600 space-y-4">
                    <h3 class="font-bold text-gray-900 border-b pb-2 text-base flex items-center gap-2">📚 Perbandingan</h3>
                    
                    <div class="space-y-2">
                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <span class="font-bold text-indigo-700 block mb-1">Linear Search</span>
                            Mengecek elemen satu per satu dari awal. Sederhana, tapi lambat untuk data besar. Tidak butuh data urut.
                        </div>
                        
                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <span class="font-bold text-purple-700 block mb-1">Binary Search</span>
                            Membagi array menjadi dua bagian terus menerus. Sangat cepat, tapi <span class="text-red-500 font-bold">wajib data terurut</span>.
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-[250px] flex flex-col">
                    <div class="bg-gray-50 px-6 py-3 border-b font-bold text-gray-700 text-sm">Logs</div>
                    <ul id="logs" class="flex-1 overflow-y-auto p-4 space-y-1 text-xs font-mono"></ul>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
    <script>
        let arr=[]; const con=document.getElementById('searchCon'); const status=document.getElementById('status'); const logList=document.getElementById('logs');
        function genSorted(){ arr=[]; con.innerHTML=''; logList.innerHTML=''; let val=0; for(let i=0;i<16;i++){ val+=Math.floor(Math.random()*5)+1; arr.push(val); } render(); document.getElementById('target').value=arr[Math.floor(Math.random()*15)]; status.innerText="Data Terurut Siap."; }
        function render(activeIdx=-1, foundIdx=-1, rangeStart=-1, rangeEnd=-1){
            con.innerHTML=''; arr.forEach((val,i)=>{ let s='bg-white border-gray-300 text-gray-600'; if(i===foundIdx)s='bg-green-500 border-green-600 text-white scale-110 shadow-lg'; else if(i===activeIdx)s='bg-yellow-300 border-yellow-500 text-yellow-800 scale-110'; else if(rangeStart!==-1 && i>=rangeStart && i<=rangeEnd)s='bg-blue-50 border-blue-200 text-blue-400'; else if(rangeStart!==-1)s='bg-gray-100 text-gray-300 border-gray-100 opacity-50';
            let b=document.createElement('div'); b.className=`w-12 h-12 border-2 rounded flex items-center justify-center font-bold text-lg transition-all duration-300 ${s}`; b.innerText=val; con.appendChild(b); });
        }
        function log(m){ logList.insertAdjacentHTML('afterbegin',`<li class="border-b border-gray-50 pb-1 text-gray-600">${m}</li>`); }
        async function linearSearch(){ let t=parseInt(document.getElementById('target').value); status.innerText="Linear Search..."; for(let i=0;i<arr.length;i++){ render(i); log(`Cek index ${i}: ${arr[i]} == ${t}?`); await new Promise(r=>setTimeout(r,400)); if(arr[i]===t){ render(-1,i); status.innerText=`Ketemu di index ${i}!`; log(`Ditemukan!`); return; } } status.innerText="Tidak ditemukan."; render(); }
        async function binarySearch(){ let t=parseInt(document.getElementById('target').value); status.innerText="Binary Search..."; let l=0,h=arr.length-1; while(l<=h){ let m=Math.floor((l+h)/2); render(m,-1,l,h); log(`Range [${l}-${h}], Mid ${m} (${arr[m]})`); await new Promise(r=>setTimeout(r,800)); if(arr[m]===t){ render(-1,m); status.innerText=`Ketemu di index ${m}!`; log(`Ditemukan!`); return; } else if(arr[m]<t){ log(`${arr[m]} < ${t}, cari kanan.`); l=m+1; } else { log(`${arr[m]} > ${t}, cari kiri.`); h=m-1; } } status.innerText="Tidak ditemukan."; render(); }
        genSorted();
    </script>
</body></html>
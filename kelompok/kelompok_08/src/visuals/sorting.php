<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Sorting"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        <div class="mb-6 flex justify-between items-center">
            <div><h1 class="text-2xl font-bold text-gray-900">📊 Bubble Sort</h1><p class="text-gray-500 text-sm">Sorting Algoritma Sederhana.</p></div>
            <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">Time: O(n²)</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 flex-1 min-h-[400px] flex items-end justify-center gap-2 pt-12" id="barsCon"></div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex justify-center gap-4">
                    <button onclick="generate()" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded-lg hover:bg-gray-200">Acak Data</button>
                    <button onclick="bubbleSort()" id="btnSort" class="px-6 py-2 bg-primary text-white font-bold rounded-lg hover:bg-primaryDark shadow-lg">Mulai Sort</button>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100 font-bold text-indigo-900 text-sm flex items-center gap-2">📚 Konsep Algoritma</div>
                    <div class="p-6 text-sm text-gray-600 space-y-4">
                        <p>Bubble Sort bekerja dengan membandingkan dua elemen yang <strong>bersebelahan</strong>. Jika urutannya salah (kiri > kanan), maka posisi mereka ditukar (Swap).</p>
                        
                        <div class="bg-yellow-50 p-3 rounded-lg border border-yellow-100 text-yellow-800 text-xs">
                            <strong>Mengapa lambat?</strong> Karena menggunakan nested loop. Untuk setiap elemen, kita harus membandingkannya dengan elemen lain berulang kali.
                        </div>

                        <div class="bg-gray-900 rounded p-4 overflow-x-auto relative group">
                            <span class="absolute top-2 right-2 text-[10px] text-gray-500 font-mono">Pseudocode</span>
                            <code class="text-xs font-mono text-yellow-300 block">
                                for i from 0 to N <br>
                                &nbsp; for j from 0 to N-i-1 <br>
                                &nbsp;&nbsp; if arr[j] > arr[j+1] <br>
                                &nbsp;&nbsp;&nbsp; swap(arr[j], arr[j+1])
                            </code>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 h-[250px] flex flex-col">
                    <div class="bg-gray-50 px-6 py-3 border-b font-bold text-gray-700 text-sm">Status</div>
                    <ul id="logs" class="flex-1 overflow-y-auto p-4 space-y-1 text-xs font-mono"></ul>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
    <script>
        let arr=[]; const con=document.getElementById('barsCon'); const logList=document.getElementById('logs'); const delay=800;
        function generate(){ arr=[]; con.innerHTML=''; for(let i=0;i<10;i++)arr.push(Math.floor(Math.random()*80)+10); render(); logList.innerHTML='<li class="text-gray-400">Data diacak.</li>'; }
        function render(active=[], sorted=[]){
            con.innerHTML=''; arr.forEach((val,i)=>{ let c='bg-indigo-400'; if(sorted.includes(i))c='bg-green-500'; else if(active.includes(i))c='bg-yellow-400';
            let b=document.createElement('div'); b.style.height=`${val}%`; b.className=`flex-1 w-full rounded-t-md transition-all duration-200 ${c} relative group`; b.innerHTML=`<span class="absolute -top-6 left-1/2 transform -translate-x-1/2 text-xs font-bold text-gray-600">${val}</span>`; con.appendChild(b); });
        }
        function log(m){ logList.insertAdjacentHTML('afterbegin',`<li class="border-b border-gray-50 pb-1 text-gray-600">${m}</li>`); }
        async function bubbleSort(){
            document.getElementById('btnSort').disabled=true; let len=arr.length; let sorted=[];
            for(let i=0;i<len;i++){
                for(let j=0;j<len-i-1;j++){
                    render([j,j+1],sorted); await new Promise(r=>setTimeout(r,delay));
                    if(arr[j]>arr[j+1]){ let t=arr[j]; arr[j]=arr[j+1]; arr[j+1]=t; log(`Swap ${arr[j]} & ${arr[j+1]}`); render([j,j+1],sorted); await new Promise(r=>setTimeout(r,delay)); }
                } sorted.push(len-i-1);
            }
            for(let k=0;k<len;k++)sorted.push(k); render([],sorted); log('Selesai! Array terurut.'); document.getElementById('btnSort').disabled=false;
        }
        generate();
    </script>
</body></html>
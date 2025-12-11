<?php 
session_start();
include '../config.php'; 
if (!isset($_SESSION['user'])) header("Location: ../index.php");

echo getHeader("Visualisasi Linked List"); 
echo getNavbar("visuals"); 
?>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <main class="flex-1 container mx-auto px-4 py-8 max-w-7xl">
        
        <div class="mb-6 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2"><span class="text-3xl">🔗</span> Singly Linked List</h1>
                <p class="text-gray-500 text-sm mt-1">Struktur data dinamis dengan Node dan Pointer.</p>
            </div>
            <div class="flex gap-2 text-xs font-bold text-white">
                <span class="px-3 py-1 bg-indigo-500 rounded-full shadow-sm">Search: O(n)</span>
                <span class="px-3 py-1 bg-purple-500 rounded-full shadow-sm">Insert Head: O(1)</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 flex flex-col gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex-1 min-h-[400px] flex flex-col">
                    <div class="flex-1 flex items-center overflow-x-auto p-4 custom-scrollbar">
                        <div id="llCon" class="flex items-center gap-0 min-w-full">
                            <div class="flex items-center opacity-50"><div class="w-16 h-16 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center text-gray-400 font-mono text-xs font-bold bg-gray-50">NULL</div></div>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-wrap gap-4 items-center justify-center">
                        <input type="number" id="val" value="9" class="w-20 px-3 py-2 border rounded-lg text-center font-bold outline-none focus:ring-2 focus:ring-primary">
                        <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
                        <button onclick="addHead()" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition shadow">Add Head</button>
                        <button onclick="addTail()" class="px-4 py-2 bg-purple-600 text-white text-sm font-bold rounded-lg hover:bg-purple-700 transition shadow">Add Tail</button>
                        <div class="w-px h-8 bg-gray-200 hidden md:block"></div>
                        <button onclick="delHead()" class="px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-lg hover:bg-red-600 transition shadow">Del Head</button>
                        <button onclick="delTail()" class="px-4 py-2 bg-orange-500 text-white text-sm font-bold rounded-lg hover:bg-orange-600 transition shadow">Del Tail</button>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-indigo-50 px-6 py-4 border-b border-indigo-100">
                        <h3 class="font-bold text-indigo-900 text-sm uppercase flex items-center gap-2">📚 Struktur Node</h3>
                    </div>
                    <div class="p-6 text-sm text-gray-600 space-y-4">
                        <p>Linked List terdiri dari elemen-elemen yang disebut <strong>Node</strong>. Setiap node memiliki dua bagian utama:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <li><strong>Data:</strong> Nilai yang disimpan (misal: angka, teks).</li>
                            <li><strong>Pointer (Next):</strong> Alamat memori yang menunjuk ke node berikutnya.</li>
                        </ul>
                        
                        <div class="bg-blue-50 p-3 rounded-lg border border-blue-100 text-blue-800 text-xs">
                            <strong>Perbedaan vs Array:</strong> Elemen Linked List <em>tidak</em> disimpan berurutan di memori. Mereka tersebar dan dihubungkan oleh pointer.
                        </div>

                        <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto relative group">
                            <span class="absolute top-2 right-2 text-[10px] text-gray-500 font-mono">C++ / Java Class</span>
                            <code class="text-xs font-mono text-blue-300 block">
                                class Node {<br>
                                &nbsp; int data;<br>
                                &nbsp; Node* next;<br>
                                }
                            </code>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[250px]">
                    <div class="bg-gray-50 px-6 py-3 border-b font-bold text-gray-700 text-sm uppercase">Log Pointer</div>
                    <ul id="logs" class="flex-1 overflow-y-auto p-4 space-y-2 text-xs font-mono"></ul>
                </div>
            </div>
        </div>
    </main>
    <?= getFooter() ?>
    <script>
        class Node { constructor(v){ this.v=v; this.next=null; } }
        let head = null; const con = document.getElementById('llCon'); const logList = document.getElementById('logs');
        function log(m){ logList.insertAdjacentHTML('afterbegin', `<li class="border-b border-gray-100 pb-1 text-gray-600">> ${m}</li>`); }
        function render(){
            con.innerHTML = ''; let curr = head;
            while(curr){
                let div = document.createElement('div'); div.className = "flex items-center animate-fade-in flex-shrink-0 group";
                div.innerHTML = `<div class="relative w-20 h-20 bg-white border-2 border-indigo-500 rounded-xl flex flex-col items-center justify-center shadow-sm z-10 group-hover:border-indigo-700 transition"><span class="text-2xl font-bold text-gray-800">${curr.v}</span><span class="text-[10px] text-gray-400 mt-1 uppercase tracking-wider">Data</span><div class="absolute top-1 right-1 w-2 h-2 bg-green-400 rounded-full"></div></div><div class="w-16 h-1 bg-indigo-200 relative flex items-center"><div class="absolute right-0 w-2 h-2 bg-indigo-200 rotate-45 transform origin-top-right -translate-y-1/2"></div><div class="absolute -top-3 w-full text-center text-[9px] text-indigo-400 font-mono">next</div></div>`;
                con.appendChild(div); curr = curr.next;
            }
            con.innerHTML += `<div class="flex items-center opacity-50 flex-shrink-0"><div class="w-16 h-16 border-2 border-dashed border-gray-400 rounded-lg flex items-center justify-center text-gray-500 font-mono text-xs font-bold bg-gray-100">NULL</div></div>`;
        }
        function addHead(){ let v=document.getElementById('val').value; let n=new Node(v); if(!head)head=n;else{n.next=head;head=n;} render(); log(`Add Head: [${v}]`); document.getElementById('val').value=Math.floor(Math.random()*20); }
        function addTail(){ let v=document.getElementById('val').value; let n=new Node(v); if(!head)head=n;else{let c=head;while(c.next)c=c.next;c.next=n;} render(); log(`Add Tail: [${v}]`); document.getElementById('val').value=Math.floor(Math.random()*20); }
        function delHead(){ if(!head)return alert('Empty'); let v=head.v; head=head.next; render(); log(`Del Head: [${v}]`); }
        function delTail(){ if(!head)return alert('Empty'); if(!head.next){log(`Del: [${head.v}]`);head=null;}else{let c=head;while(c.next.next)c=c.next;log(`Del: [${c.next.v}]`);c.next=null;} render(); }
    </script>
    <style>.custom-scrollbar::-webkit-scrollbar{height:8px}.custom-scrollbar::-webkit-scrollbar-track{background:#f1f1f1}.custom-scrollbar::-webkit-scrollbar-thumb{background:#c7c7c7;border-radius:4px}</style>
</body></html>
// Lokasi file: src/frontend/admin/js/admin.js

document.addEventListener('DOMContentLoaded', () => {
    
    // --- API Configuration (Harus sesuai dengan auth.php) ---
    const BASE_API_URL =
        "http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/backend/api/auth.php"; 
    const PENDING_MEMBERS_API_URL = `${BASE_API_URL}?action=pending_members`;
    const APPROVE_MEMBER_API_URL = `${BASE_API_URL}?action=approve_member`;
    const LOGOUT_API_URL = `${BASE_API_URL}?action=logout`; // URL untuk Logout API
    
    const LOGIN_PAGE_URL = "http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_17/src/frontend/auth/login.html"; // URL halaman Login
    
    const pendingMembersList = document.getElementById('pending-members-list');
    const statsContainer = document.getElementById('stats-container');
    const agendaList = document.getElementById('agenda-list');
    const adminMessage = document.getElementById('adminMessage'); // Dari admin.html
    const logoutButtonDesktop = document.getElementById('btn-logout-desktop'); // ID tombol keluar desktop
    const logoutButtonMobile = document.getElementById('btn-logout-mobile'); // ID tombol keluar mobile

    // --- 1. Data Simulation (Dari kode Anda) ---
    const statsData = [
        { title: "TOTAL ANGGOTA", value: "1,247", icon: "fas fa-user-group", status: "Status Keanggotaan Terdaftar", id: "stats-card-1" },
        { title: "AGENDA BULAN INI", value: "23", icon: "fas fa-calendar-check", status: "Kegiatan Terpublikasi", id: "stats-card-2" },
        { title: "ANGGOTA NON-AKTIF", value: "89", icon: "fas fa-user-times", status: "Perlu Tindak Lanjut (SP)", id: "stats-card-3" },
        { title: "RATA-RATA PRESENSI (%)", value: "88.5", icon: "fas fa-chart-line", status: "Target Kehadiran Kritis", id: "stats-card-4" }
    ];

    const agendaData = [
        { title: "Electrical Engineering in Action (EEA) 2025", date: "Senin, 15 Des 2025", time: "09:00 WIB", location: "Ruang Serbaguna A", icon: "fas fa-laptop-code", isZoom: false },
        { title: "Rapat Koordinasi Divisi Baru", date: "Rabu, 17 Des 2025", time: "14:00 WIB", location: "Zoom Meeting", icon: "fas fa-users-gear", isZoom: true }
    ];


    // --- 2. Helper Functions ---
    const safeJson = async (res) => { 
        try { 
            return await res.json(); 
        } catch (e) { 
            console.error('JSON Parse Error:', e);
            return null; 
        } 
    };

    const displayMessage = (message, type) => {
        if (!adminMessage) return;
        adminMessage.classList.remove("d-none", "alert-danger", "alert-success", "alert-warning");
        adminMessage.classList.add(`alert-${type}`);
        adminMessage.textContent = message;
        
        // Sembunyikan pesan setelah 5 detik
        if (type !== 'd-none') {
            setTimeout(() => {
                adminMessage.classList.add("d-none");
                adminMessage.textContent = '';
            }, 5000);
        }
    };


    // --- 3. Data Rendering (Kode Anda) ---
    // (renderStatsCards dan renderAgendaList Dihilangkan untuk keringkasan)
    
    function renderStatsCards(container) {
        // [Kode renderStatsCards Anda]
        container.innerHTML = ''; 
        statsData.forEach((stat) => {
            let statusClass;
            if (stat.status.includes('Terdaftar')) {
                statusClass = 'status-text-positive';
            } else if (stat.status.includes('Perlu Tindak Lanjut') || stat.status.includes('Kritis')) {
                statusClass = 'status-text-negative';
            } else {
                 statusClass = 'status-text-neutral';
            }
            const html = `
                <div class="bg-white p-5 card-ringkasan-base shadow-lg hover:shadow-xl" id="${stat.id}">
                    <div class="flex flex-col">
                        <p class="text-xs font-medium text-gray-500 mb-4">${stat.title}</p>
                        <div class="flex items-center justify-between">
                            <p class="text-4xl font-extrabold text-gray-900">${stat.value}</p>
                            <div class="card-icon-circle">
                                <i class="${stat.icon}"></i>
                            </div>
                        </div>
                        <p class="text-sm mt-3 ${statusClass}">${stat.status}</p>
                    </div>
                </div>
            `;
            container.innerHTML += html;
        });
    }

    function renderAgendaList(list) {
        // [Kode renderAgendaList Anda]
        list.innerHTML = ''; 
        agendaData.forEach((item) => {
            const locationIcon = item.isZoom ? 'fas fa-video' : 'fas fa-map-marker-alt';
            const html = `
                <div class="p-4 border-b last:border-b-0 border-gray-100 flex justify-between items-start">
                    <div class="flex space-x-3 w-3/4">
                        <div class="text-2xl text-primary-brand mt-1"><i class="${item.icon}"></i></div>
                        <div>
                            <p class="font-semibold text-gray-800 mb-1">${item.title}</p>
                            <p class="text-xs text-gray-500 flex items-center mt-1">
                                <i class="fas fa-clock mr-1"></i> ${item.date}, ${item.time}
                            </p>
                            <p class="text-xs text-gray-500 flex items-center">
                                <i class="${locationIcon} mr-1"></i> ${item.location}
                            </p>
                        </div>
                    </div>
                    <button class="btn-agenda-action">Kelola Agenda</button>
                </div>
            `;
            list.innerHTML += html;
        });
    }


    // --- 4. Logika Persetujuan Anggota ---

    async function fetchAndRenderPendingMembers() {
        if (!pendingMembersList) return;

        pendingMembersList.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-gray-500">Memuat anggota pending...</td></tr>';
        displayMessage('', 'd-none'); 
        
        try {
            const response = await fetch(PENDING_MEMBERS_API_URL, { method: "GET", credentials: "include" });
            const result = await safeJson(response);

            if (result && result.status === "success" && Array.isArray(result.data)) {
                
                const members = result.data;
                if (members.length === 0) {
                    pendingMembersList.innerHTML = '<tr><td colspan="5" class="text-center py-4 font-medium text-green-600">🎉 Semua pendaftaran telah disetujui.</td></tr>';
                    pendingMembersList.removeEventListener('click', delegateApprovalAction);
                    return;
                }

                pendingMembersList.innerHTML = '';
                members.forEach(member => {
                    const row = `
                        <tr class="hover:bg-gray-50 border-b">
                            <td class="px-4 py-3">${member.user_id}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">${member.full_name || member.username || 'N/A'}</td>
                            <td class="px-4 py-3">${member.email}</td>
                            <td class="px-4 py-3">
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2.5 py-0.5 rounded">Pending</span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <button class="btn-approve bg-green-500 hover:bg-green-600 text-white px-3 py-1 text-sm rounded mr-2" 
                                    data-member-id="${member.user_id}">Setujui</button>
                                <button class="btn-reject bg-red-500 hover:bg-red-600 text-white px-3 py-1 text-sm rounded" 
                                    data-member-id="${member.user_id}">Tolak</button>
                            </td>
                        </tr>
                    `;
                    pendingMembersList.innerHTML += row;
                });
                
                // Panggil listener delegasi setelah data dimuat
                pendingMembersList.addEventListener('click', delegateApprovalAction);

            } else {
                displayMessage(`Gagal memuat data: ${(result && result.message) || 'Kesalahan API.'}`, 'danger');
                pendingMembersList.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-red-500">Gagal memuat data: ${(result && result.message) || 'Kesalahan API.'}</td></tr>`;
            }

        } catch (error) {
            console.error('Network Error:', error);
            pendingMembersList.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-red-500">Kesalahan jaringan atau server.</td></tr>';
        }
    }

    // Menggunakan Event Delegation untuk menangani klik tombol (Lebih efisien)
    function delegateApprovalAction(e) {
        const button = e.target;
        if (button.classList.contains('btn-approve') || button.classList.contains('btn-reject')) {
            const memberId = button.getAttribute('data-member-id');
            const action = button.classList.contains('btn-approve') ? 'approved' : 'rejected';
            
            if (memberId) {
                 handleApprovalAction(button, memberId, action);
            }
        }
    }

    async function handleApprovalAction(button, memberId, action) {
        const originalText = button.textContent;
        const allButtons = document.querySelectorAll(`[data-member-id="${memberId}"]`);
        
        // Menonaktifkan semua tombol untuk user ini
        allButtons.forEach(btn => {
            btn.disabled = true;
            if (btn === button) btn.textContent = 'Memproses...';
        });

        displayMessage('', 'd-none'); 

        try {
            const response = await fetch(APPROVE_MEMBER_API_URL, {
                method: "POST",
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    member_id: memberId,
                    status: action
                }),
                credentials: "include"
            });
            const result = await safeJson(response);

            if (result && result.status === "success") {
                displayMessage(result.message, 'success'); 
                fetchAndRenderPendingMembers(); // Muat ulang daftar
            } else {
                displayMessage(`Gagal ${action}: ${(result && result.message) || 'Kesalahan API.'}`, 'danger');
                // Aktifkan kembali tombol jika gagal
                allButtons.forEach(btn => {
                    btn.disabled = false;
                    btn.textContent = btn.classList.contains('btn-approve') ? 'Setujui' : 'Tolak';
                });
            }

        } catch (err) {
            console.error('Network Error:', err);
            displayMessage('Kesalahan jaringan saat memproses permintaan.', 'danger');
            // Aktifkan kembali tombol jika gagal
            allButtons.forEach(btn => {
                btn.disabled = false;
                btn.textContent = btn.classList.contains('btn-approve') ? 'Setujui' : 'Tolak';
            });
        }
    }


    // ----------------------------------------------------------------------
    // 🆕 NEW: FUNGSI LOGOUT DAN REDIRECT
    // ----------------------------------------------------------------------
    async function handleLogout(e) {
        e.preventDefault();
        displayMessage('Logging out...', 'info');

        try {
            const response = await fetch(LOGOUT_API_URL, {
                method: 'POST',
                credentials: 'include' // Kirim cookie sesi
            });
            const result = await safeJson(response);

            // Jika backend merespons sukses (atau bahkan gagal tapi kita tetap ingin mengalihkan)
            if (result && result.status === 'success') {
                displayMessage(result.message || 'Logout berhasil.', 'success');
            } else {
                 // Jika API error, log out secara paksa di frontend
                console.error('Logout API failed:', result.message);
                displayMessage('Logout berhasil (Sesi diakhiri secara lokal).', 'warning');
            }

            // Alihkan ke halaman login
            setTimeout(() => {
                window.location.href = LOGIN_PAGE_URL; 
            }, 500);

        } catch (error) {
            console.error('Network error during logout:', error);
            displayMessage('Gagal terhubung ke server saat logout. Mengalihkan secara paksa.', 'danger');
            // Alihkan secara paksa setelah delay
            setTimeout(() => {
                window.location.href = LOGIN_PAGE_URL;
            }, 1000);
        }
    }
    // ----------------------------------------------------------------------


    // --- 5. Initialization (Memuat Data Dashboard & Anggota Pending) ---

    function initializeDashboard() {
        // Render Dashboard Stats dan Agenda
        if (statsContainer) renderStatsCards(statsContainer);
        if (agendaList) renderAgendaList(agendaList);
        
        // Fetch Anggota Pending
        fetchAndRenderPendingMembers();
        
        // Pasang Event Listeners untuk Logout
        if (logoutButtonDesktop) {
            logoutButtonDesktop.addEventListener('click', handleLogout);
        }
        if (logoutButtonMobile) {
            logoutButtonMobile.addEventListener('click', handleLogout);
        }
    }
    
    // Panggil fungsi inisialisasi
    initializeDashboard();


    // --- 6. Sidebar Toggle for Mobile Responsiveness (Same as before) ---
    window.toggleMobileSidebar = function() {
        const sidebar = document.getElementById('sidebar-mobile');
        const overlay = document.getElementById('sidebar-mobile-overlay');
        
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.classList.toggle('overflow-hidden');
    }

});
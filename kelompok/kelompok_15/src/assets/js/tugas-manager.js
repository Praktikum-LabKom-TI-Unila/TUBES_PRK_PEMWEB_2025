/**
 * TUGAS MANAGEMENT - API INTEGRATION
 * Fitur 4: Manajemen Tugas (Dosen)
 * 
 * Handles:
 * - Create/Edit/Delete Tugas
 * - Get Submissions List
 * - Beri Nilai & Feedback
 * - Real-time status calculation
 */

const TugasManager = {
    API_BASE: '../backend',
    currentKelasId: null,
    currentTugasId: null,
    currentSubmissionId: null,

    /**
     * Initialize - Get kelas ID from URL/session
     */
    init() {
        const params = new URLSearchParams(window.location.search);
        this.currentKelasId = params.get('id_kelas') || localStorage.getItem('current_kelas_id');
        
        if (this.currentKelasId) {
            this.loadTugasList();
        } else {
            this.showError('ID Kelas tidak ditemukan. Silakan kembali ke dashboard.');
        }
    },

    /**
     * Load semua tugas untuk kelas
     */
    async loadTugasList() {
        try {
            const response = await fetch(
                `${this.API_BASE}/tugas/get-tugas.php?id_kelas=${this.currentKelasId}`
            );
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.renderTugasList(result.data);
        } catch (error) {
            console.error('Error loading tugas:', error);
            this.showError('Gagal memuat tugas: ' + error.message);
        }
    },

    /**
     * Render tugas list ke halaman
     */
    renderTugasList(tugasList) {
        const container = document.getElementById('tugasListContainer');
        if (!container) return;

        if (tugasList.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-gray-600 text-lg">Belum ada tugas</p>
                </div>
            `;
            return;
        }

        // Update total count
        const totalElement = document.getElementById('totalTugas');
        if (totalElement) {
            totalElement.textContent = tugasList.length;
        }

        container.innerHTML = tugasList.map(tugas => this.createTugasCard(tugas)).join('');
    },

    /**
     * Create HTML card untuk satu tugas
     */
    createTugasCard(tugas) {
        const deadline = new Date(tugas.deadline);
        const now = new Date();
        const isExpired = deadline < now;
        const statusBadge = tugas.status === 'active' 
            ? '<span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">Active</span>'
            : '<span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">Expired</span>';

        const progressPercent = tugas.jumlah_submission > 0 
            ? Math.round((tugas.jumlah_dinilai / tugas.jumlah_submission) * 100)
            : 0;

        return `
            <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all p-6 animate-slideIn">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-gray-800 mb-2">${this.escapeHtml(tugas.judul)}</h3>
                        <p class="text-gray-600 text-sm">${this.escapeHtml(tugas.deskripsi || 'Tidak ada deskripsi')}</p>
                    </div>
                    ${statusBadge}
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4 py-4 border-y border-gray-200">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-blue-600">${tugas.jumlah_submission}</p>
                        <p class="text-xs text-gray-600">Submission</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-green-600">${tugas.jumlah_dinilai}</p>
                        <p class="text-xs text-gray-600">Dinilai</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-purple-600">${tugas.bobot || 0}</p>
                        <p class="text-xs text-gray-600">Bobot</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-600">Progress Penilaian</span>
                        <span class="font-semibold">${progressPercent}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-full transition-all duration-300" style="width: ${progressPercent}%"></div>
                    </div>
                </div>

                <div class="flex gap-2">
                    <button onclick="TugasManager.viewSubmissions(${tugas.id_tugas})" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        👁️ Lihat Submission
                    </button>
                    <button onclick="TugasManager.editTugas(${tugas.id_tugas})" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        ✏️ Edit
                    </button>
                    <button onclick="TugasManager.deleteTugas(${tugas.id_tugas})" 
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        🗑️ Hapus
                    </button>
                </div>
            </div>
        `;
    },

    /**
     * Create Tugas - Modal
     */
    openCreateTugasModal() {
        const modal = document.getElementById('modalTugas');
        if (!modal) return;

        document.getElementById('modalTitle').textContent = 'Buat Tugas Baru';
        document.getElementById('formTugas').reset();
        modal.classList.remove('hidden');
    },

    /**
     * Submit Create Tugas
     */
    async submitCreateTugas(event) {
        event.preventDefault();
        
        const formData = new FormData(document.getElementById('formTugas'));
        formData.append('id_kelas', this.currentKelasId);

        try {
            const response = await fetch(`${this.API_BASE}/tugas/create-tugas.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.showSuccess('Tugas berhasil dibuat!');
            document.getElementById('modalTugas').classList.add('hidden');
            this.loadTugasList();
        } catch (error) {
            this.showError('Gagal membuat tugas: ' + error.message);
        }
    },

    /**
     * Edit Tugas
     */
    async editTugas(id_tugas) {
        this.currentTugasId = id_tugas;
        document.getElementById('modalTitle').textContent = 'Edit Tugas';
        
        // Load tugas data dan populate form
        try {
            // Fetch tugas data (need to implement endpoint or get from list)
            // For now, just open modal
            document.getElementById('modalTugas').classList.remove('hidden');

        } catch (error) {
            this.showError('Gagal memuat data tugas: ' + error.message);
        }
    },

    /**
     * Submit Edit Tugas
     */
    async submitEditTugas(event) {
        event.preventDefault();
        
        const formData = new FormData(document.getElementById('formTugas'));
        formData.append('id_tugas', this.currentTugasId);

        try {
            const response = await fetch(`${this.API_BASE}/tugas/update-tugas.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.showSuccess('Tugas berhasil diupdate!');
            document.getElementById('modalTugas').classList.add('hidden');
            this.loadTugasList();
        } catch (error) {
            this.showError('Gagal mengupdate tugas: ' + error.message);
        }
    },

    /**
     * Delete Tugas - Confirmation
     */
    async deleteTugas(id_tugas) {
        if (!confirm('Yakin ingin menghapus tugas ini? Data submissions akan ikut terhapus.')) {
            return;
        }

        try {
            const response = await fetch(`${this.API_BASE}/tugas/delete-tugas.php`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `id_tugas=${id_tugas}`
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.showSuccess('Tugas berhasil dihapus!');
            this.loadTugasList();
        } catch (error) {
            this.showError('Gagal menghapus tugas: ' + error.message);
        }
    },

    /**
     * View Submissions untuk tugas tertentu
     */
    async viewSubmissions(id_tugas) {
        this.currentTugasId = id_tugas;
        
        try {
            const response = await fetch(
                `${this.API_BASE}/tugas/get-submissions.php?id_tugas=${id_tugas}`
            );
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            // Redirect ke halaman lihat-submission atau update modal
            window.location.href = `lihat-submission.php?id_tugas=${id_tugas}`;
        } catch (error) {
            this.showError('Gagal memuat submissions: ' + error.message);
        }
    },

    /**
     * Get Submissions List (untuk lihat-submission.php)
     */
    async loadSubmissionsList(id_tugas) {
        try {
            const response = await fetch(
                `${this.API_BASE}/tugas/get-submissions.php?id_tugas=${id_tugas}`
            );
            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.renderSubmissionsList(result.data);
        } catch (error) {
            console.error('Error loading submissions:', error);
            this.showError('Gagal memuat submissions: ' + error.message);
        }
    },

    /**
     * Render Submissions Table
     */
    renderSubmissionsList(submissions) {
        const tbody = document.getElementById('submissionsTableBody');
        if (!tbody) return;

        if (submissions.length === 0) {
            tbody.innerHTML = `
                <tr><td colspan="5" class="text-center py-8 text-gray-600">Belum ada submission</td></tr>
            `;
            return;
        }

        tbody.innerHTML = submissions.map((sub, idx) => `
            <tr class="border-b hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">${idx + 1}</td>
                <td class="px-6 py-4">
                    <div>
                        <p class="font-semibold text-gray-900">${this.escapeHtml(sub.nama_mahasiswa)}</p>
                        <p class="text-sm text-gray-600">${sub.npm_nidn}</p>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold ${
                        sub.status === 'on_time' ? 'bg-green-100 text-green-800' : 
                        sub.status === 'late' ? 'bg-yellow-100 text-yellow-800' : 
                        'bg-gray-100 text-gray-800'
                    }">
                        ${sub.status === 'on_time' ? '✓ On Time' : sub.status === 'late' ? '⚠ Late' : 'Not Submitted'}
                    </span>
                </td>
                <td class="px-6 py-4 text-center">
                    ${sub.nilai ? `<span class="text-lg font-bold text-blue-600">${sub.nilai}</span>` : 
                      '<span class="text-gray-500">-</span>'}
                </td>
                <td class="px-6 py-4">
                    <button onclick="TugasManager.openNilaiModal(${sub.id_submission})" 
                            class="px-4 py-2 rounded-lg text-white font-semibold ${
                                sub.nilai ? 'bg-orange-600 hover:bg-orange-700' : 'bg-blue-600 hover:bg-blue-700'
                            } transition-colors">
                        ${sub.nilai ? '✏️ Edit Nilai' : '⭐ Beri Nilai'}
                    </button>
                </td>
            </tr>
        `).join('');
    },

    /**
     * Open Nilai Modal
     */
    openNilaiModal(id_submission) {
        this.currentSubmissionId = id_submission;
        const modal = document.getElementById('modalNilai');
        if (!modal) return;

        // Reset form
        document.getElementById('formNilai').reset();
        modal.classList.remove('hidden');
    },

    /**
     * Submit Nilai & Feedback
     */
    async submitNilai(event) {
        event.preventDefault();

        const nilai = document.getElementById('inputNilai').value;
        const feedback = document.getElementById('inputFeedback').value;

        if (!nilai || nilai < 0 || nilai > 100) {
            this.showError('Nilai harus antara 0-100');
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id_submission', this.currentSubmissionId);
            formData.append('nilai', nilai);
            formData.append('feedback', feedback);

            const response = await fetch(`${this.API_BASE}/tugas/nilai-tugas.php`, {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message);
            }

            this.showSuccess('Nilai berhasil disimpan!');
            document.getElementById('modalNilai').classList.add('hidden');
            
            // Reload submissions list
            const params = new URLSearchParams(window.location.search);
            const id_tugas = params.get('id_tugas');
            if (id_tugas) {
                this.loadSubmissionsList(id_tugas);
            }
        } catch (error) {
            this.showError('Gagal menyimpan nilai: ' + error.message);
        }
    },

    /**
     * Close Modal
     */
    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('hidden');
        }
    },

    /**
     * Show Error Message
     */
    showError(message) {
        const div = document.createElement('div');
        div.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slideIn';
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    },

    /**
     * Show Success Message
     */
    showSuccess(message) {
        const div = document.createElement('div');
        div.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg animate-slideIn';
        div.textContent = message;
        document.body.appendChild(div);
        setTimeout(() => div.remove(), 3000);
    },

    /**
     * Escape HTML
     */
    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on kelola-tugas.php or lihat-submission.php
    if (window.location.pathname.includes('kelola-tugas.php')) {
        TugasManager.init();
    } else if (window.location.pathname.includes('lihat-submission.php')) {
        const params = new URLSearchParams(window.location.search);
        const id_tugas = params.get('id_tugas');
        if (id_tugas) {
            TugasManager.loadSubmissionsList(id_tugas);
        }
    }
});

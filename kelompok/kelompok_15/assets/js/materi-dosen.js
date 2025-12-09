/**
 * MATERI DOSEN - AJAX Management
 * Fitur:
 * - Upload PDF dengan progress indicator
 * - Add video links (YouTube, Google Drive)
 * - Edit & Delete materi
 * - Real-time UI updates
 */

// ============================================
// LOAD MATERI LIST
// ============================================

async function loadMateriBatch(id_kelas) {
    try {
        const response = await fetch(`../backend/materi/get-materi.php?id_kelas=${id_kelas}`);
        const data = await response.json();

        if (data.success) {
            displayMateriBatch(data.data);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Error loading materi: ' + error.message);
    }
}

function displayMateriBatch(grouped) {
    const container = document.getElementById('materiBatchContainer');
    if (!container) return;

    container.innerHTML = '';

    Object.keys(grouped).sort((a, b) => a - b).forEach(pertemuan => {
        const materis = grouped[pertemuan];
        
        const batchHtml = `
            <div class="materi-batch mb-6 border border-gray-200 rounded-lg p-4 bg-white">
                <div class="batch-header flex items-center justify-between mb-4">
                    <h4 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-book-open text-blue-500 mr-2"></i>
                        Pertemuan ${pertemuan}
                    </h4>
                    <span class="badge bg-blue-100 text-blue-700 px-3 py-1 rounded">${materis.length} item</span>
                </div>

                <div class="materi-items space-y-2">
                    ${materis.map(m => `
                        <div class="materi-item flex items-start justify-between p-3 bg-gray-50 rounded hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800">${m.judul}</p>
                                <p class="text-sm text-gray-600">${m.deskripsi || ''}</p>
                                <p class="text-xs text-gray-500 mt-1">
                                    ${m.tipe === 'pdf' ? '<i class="fas fa-file-pdf text-red-500"></i> PDF' : '<i class="fas fa-video text-blue-500"></i> Video'}
                                </p>
                            </div>
                            <div class="flex gap-2 ml-4">
                                ${m.tipe === 'pdf' ? `
                                    <a href="../backend/materi/download-materi.php?id=${m.id_materi}" 
                                       class="btn btn-sm btn-primary" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                ` : ''}
                                <button onclick="editMateriBatch(${m.id_materi})" class="btn btn-sm btn-info">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="deleteMateriBatch(${m.id_materi})" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;

        container.innerHTML += batchHtml;
    });
}

// ============================================
// UPLOAD PDF DENGAN PROGRESS
// ============================================

function setupFileUploadHandler() {
    const fileInput = document.getElementById('materi_file');
    const uploadBtn = document.getElementById('uploadMateriBtnPDF');
    
    if (!fileInput || !uploadBtn) return;

    uploadBtn.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            uploadPdfMateri(e.target.files[0]);
        }
    });
}

async function uploadPdfMateri(file) {
    const id_kelas = document.getElementById('id_kelas_materi')?.value;
    const judul = document.getElementById('judul_materi')?.value;
    const deskripsi = document.getElementById('deskripsi_materi')?.value;
    const pertemuan = document.getElementById('pertemuan_materi')?.value;

    if (!id_kelas || !judul || !pertemuan) {
        showError('Please fill all required fields');
        return;
    }

    // Validate file
    if (!file.name.endsWith('.pdf')) {
        showError('Only PDF files are allowed');
        return;
    }

    if (file.size > 10 * 1024 * 1024) {
        showError('File size must be less than 10MB');
        return;
    }

    const formData = new FormData();
    formData.append('id_kelas', id_kelas);
    formData.append('judul', judul);
    formData.append('deskripsi', deskripsi);
    formData.append('pertemuan_ke', pertemuan);
    formData.append('file', file);

    try {
        const xhr = new XMLHttpRequest();
        
        // Progress event
        xhr.upload.addEventListener('progress', (e) => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                updateUploadProgress(percentComplete);
            }
        });

        xhr.addEventListener('load', () => {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    showSuccess('File uploaded successfully');
                    resetUploadForm();
                    loadMateriBatch(id_kelas);
                } else {
                    showError(response.message);
                }
            } else {
                showError('Upload failed: ' + xhr.status);
            }
        });

        xhr.addEventListener('error', () => {
            showError('Upload error');
        });

        xhr.open('POST', '../backend/materi/upload-materi.php');
        xhr.send(formData);

    } catch (error) {
        showError('Error: ' + error.message);
    }
}

function updateUploadProgress(percent) {
    const progressBar = document.getElementById('uploadProgress');
    const progressText = document.getElementById('uploadProgressText');

    if (progressBar) {
        progressBar.style.width = percent + '%';
        progressBar.textContent = Math.round(percent) + '%';
    }

    if (progressText) {
        progressText.textContent = Math.round(percent) + '%';
    }
}

function resetUploadForm() {
    const form = document.getElementById('uploadMateriForms');
    if (form) form.reset();

    const progressBar = document.getElementById('uploadProgress');
    if (progressBar) {
        progressBar.style.width = '0%';
        progressBar.textContent = '';
    }
}

// ============================================
// ADD VIDEO LINK
// ============================================

async function addVideoMateri() {
    const id_kelas = document.getElementById('id_kelas_video')?.value;
    const judul = document.getElementById('judul_video')?.value;
    const video_url = document.getElementById('video_url')?.value;
    const deskripsi = document.getElementById('deskripsi_video')?.value;
    const pertemuan = document.getElementById('pertemuan_video')?.value;

    if (!id_kelas || !judul || !video_url || !pertemuan) {
        showError('Please fill all required fields');
        return;
    }

    const formData = new FormData();
    formData.append('id_kelas', id_kelas);
    formData.append('judul', judul);
    formData.append('video_url', video_url);
    formData.append('deskripsi', deskripsi);
    formData.append('pertemuan_ke', pertemuan);

    try {
        const response = await fetch('../backend/materi/add-video.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Video added successfully');
            resetVideoForm();
            loadMateriBatch(id_kelas);
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Error: ' + error.message);
    }
}

function resetVideoForm() {
    const form = document.getElementById('addVideoForm');
    if (form) form.reset();
}

// ============================================
// EDIT MATERI
// ============================================

async function editMateriBatch(id_materi) {
    // Show edit modal with current data
    // Implementation depends on your modal structure
    showError('Edit functionality - to be implemented in modal');
}

async function updateMateriBatch(id_materi) {
    const judul = document.getElementById('edit_judul')?.value;
    const deskripsi = document.getElementById('edit_deskripsi')?.value;
    const pertemuan = document.getElementById('edit_pertemuan')?.value;

    const formData = new FormData();
    formData.append('id_materi', id_materi);
    
    if (judul) formData.append('judul', judul);
    if (deskripsi) formData.append('deskripsi', deskripsi);
    if (pertemuan) formData.append('pertemuan_ke', pertemuan);

    // Handle file if new file selected
    const fileInput = document.getElementById('edit_file');
    if (fileInput && fileInput.files.length > 0) {
        formData.append('file', fileInput.files[0]);
    }

    try {
        const response = await fetch('../backend/materi/update-materi.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Materi updated successfully');
            closeEditModal();
            loadMateriBatch(getCurrentClassId());
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Error: ' + error.message);
    }
}

// ============================================
// DELETE MATERI
// ============================================

async function deleteMateriBatch(id_materi) {
    if (!confirm('Are you sure you want to delete this material?')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('id_materi', id_materi);

        const response = await fetch('../backend/materi/delete-materi.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            showSuccess('Materi deleted successfully');
            loadMateriBatch(getCurrentClassId());
        } else {
            showError(data.message);
        }
    } catch (error) {
        showError('Error: ' + error.message);
    }
}

// ============================================
// UTILITY FUNCTIONS
// ============================================

function showError(message) {
    console.error(message);
    alert('Error: ' + message);
}

function showSuccess(message) {
    console.log(message);
    alert(message);
}

function getCurrentClassId() {
    return document.getElementById('id_kelas_materi')?.value ||
           document.getElementById('id_kelas_video')?.value;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
    setupFileUploadHandler();
});

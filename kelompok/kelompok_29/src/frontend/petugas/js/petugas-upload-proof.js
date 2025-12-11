// File: js/petugas-upload-proof.js

let selectedImage = window.taskInitialData.completionImageUrl; // Ambil nilai awal dari PHP
const taskId = window.taskInitialData.id;
const imageUrlBefore = window.taskInitialData.imageUrlBefore;

// --- 1. FUNGSI UTAMA RENDERING (Image Uploader dan Preview) ---

function renderImageUploader() {
    const container = document.getElementById('imagePreviewContainer');
    let html = '';

    if (selectedImage) {
        // Render Preview
        html = `
            <div class="relative mb-6">
                <img src="${selectedImage}" alt="Preview" class="w-full h-64 object-cover rounded-xl" />
                <button
                    type="button"
                    onclick="handleDeleteImage()"
                    class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-lg hover:bg-red-700 text-sm font-medium"
                >
                    Hapus
                </button>
            </div>
            
            <div class="bg-white p-6 border border-gray-200 rounded-xl">
                <h3 class="mb-4 text-lg font-semibold">Perbandingan Sebelum & Sesudah</h3>
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-700 mb-2">Sebelum</p>
                        <img src="${imageUrlBefore}" alt="Sebelum" class="w-full h-48 object-cover rounded-lg" />
                    </div>
                    <div>
                        <p class="text-gray-700 mb-2">Sesudah</p>
                        <img src="${selectedImage}" alt="Sesudah" class="w-full h-48 object-cover rounded-lg" />
                    </div>
                </div>
            </div>
        `;
    } else {
        // Render Uploader
        html = `
            <label class="block border-2 border-dashed border-gray-300 rounded-xl p-12 text-center cursor-pointer hover:border-blue-600 hover:bg-blue-50 transition-all">
                <i class="material-icons text-6xl text-gray-400 mx-auto mb-3">cloud_upload</i>
                <p class="text-gray-600 font-medium mb-1">Klik untuk upload foto</p>
                <p class="text-gray-400 text-sm">JPG, PNG (Max 5MB)</p>
                <input
                    type="file"
                    accept="image/*"
                    onchange="handleImageUpload(event)"
                    class="hidden"
                />
            </label>
        `;
    }
    container.innerHTML = html;
}

// --- 2. HANDLER INTERAKSI ---

function handleImageUpload(e) {
    const file = e.target.files?.[0];
    if (file) {
        // Batasi ukuran file (opsional)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5MB.');
            return;
        }

        const reader = new FileReader();
        reader.onloadend = () => {
            selectedImage = reader.result; // DataURL
            renderImageUploader();
        };
        reader.readAsDataURL(file);
    }
}

function handleDeleteImage() {
    selectedImage = null;
    renderImageUploader();
}

document.getElementById('uploadProofForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const notes = document.getElementById('notes').value.trim();
    const isEdit = !!window.taskInitialData.completionImageUrl;
    
    // Validasi
    if (!selectedImage) {
        alert('Foto bukti penyelesaian wajib diupload!');
        return;
    }
    if (!notes) {
        alert('Catatan penyelesaian wajib diisi!');
        return;
    }
    
    // --- SIMULASI KIRIM DATA KE PHP ---
    const formData = new FormData();
    formData.append('taskId', taskId);
    formData.append('photoDataUrl', selectedImage); // Kirim sebagai DataURL (untuk simulasi)
    formData.append('notes', notes);
    
    console.log(`Mengirim bukti untuk tugas ${taskId}. Edit: ${isEdit}`);
    
    const message = isEdit ? 'Bukti penyelesaian berhasil diperbarui!' : 'Bukti penyelesaian berhasil dikirim! Status: Menunggu validasi dari admin.';
    
    alert(message);
    
    // Arahkan kembali ke Detail Tugas
    window.location.href = `petugas-task-detail.php?id=${taskId}`;
});

// --- EKSEKUSI AWAL ---
document.addEventListener('DOMContentLoaded', () => {
    // Memastikan textarea diisi dengan data awal (jika ada)
    document.getElementById('notes').value = window.taskInitialData.officerNotes || '';
    renderImageUploader();
});
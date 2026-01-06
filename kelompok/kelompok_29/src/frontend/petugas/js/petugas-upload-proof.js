const taskId = window.currentTaskId;
const placeholderImage = 'https://placehold.co/300x200?text=Foto';
const form = document.getElementById('uploadProofForm');
const photoInput = document.getElementById('photoAfterInput');
const photoPreview = document.getElementById('photoAfterPreview');
const notesInput = document.getElementById('notes');
const alertBox = document.getElementById('uploadAlert');
const submitButton = document.getElementById('submitProofButton');
const defaultButtonContent = submitButton.innerHTML;
const editButtonContent = '<i class="material-icons text-xl">save</i> Perbarui Bukti';

const taskTitleText = document.getElementById('taskTitleText');
const taskCategoryText = document.getElementById('taskCategoryText');
const taskLocationText = document.getElementById('taskLocationText');
const taskBeforeImage = document.getElementById('taskBeforeImage');
const taskBeforeThumbnail = document.getElementById('taskBeforeThumbnail');

let currentTask = null;
let existingProof = null;

function showUploadAlert(message, type = 'error') {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-200', 'bg-green-50', 'text-green-700', 'border-green-200');
    if (type === 'success') {
        alertBox.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
    } else {
        alertBox.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
    }
}

function clearUploadAlert() {
    alertBox?.classList.add('hidden');
    if (alertBox) alertBox.textContent = '';
}

function fillTaskInfo(detail) {
    const cover = PetugasAuth.resolveFileUrl(detail.photos?.before || detail.cover_photo || '') || placeholderImage;
    taskTitleText.textContent = detail.title || '-';
    taskCategoryText.textContent = detail.category || '-';
    taskLocationText.textContent = detail.address || '-';
    taskBeforeImage.src = cover;
    taskBeforeThumbnail.src = cover;
    taskBeforeImage.onerror = () => (taskBeforeImage.src = placeholderImage);
    taskBeforeThumbnail.onerror = () => (taskBeforeThumbnail.src = placeholderImage);
}

async function loadTaskDetail() {
    if (!taskId) {
        showUploadAlert('ID tugas tidak ditemukan.');
        submitButton.disabled = true;
        return;
    }
    try {
        const payload = await PetugasAPI.request(`/officer/tasks/${taskId}`);
        currentTask = payload.data || {};
        fillTaskInfo(currentTask);
        if (!['dalam_proses', 'menunggu_validasi_admin'].includes(currentTask.status)) {
            showUploadAlert('Tugas ini belum dapat mengunggah bukti.', 'error');
            submitButton.disabled = true;
            return;
        }
        if (currentTask.status === 'menunggu_validasi_admin') {
            await loadExistingProof();
        } else {
            submitButton.innerHTML = defaultButtonContent;
        }
    } catch (error) {
        console.error('[UploadProof] load detail error', error);
        showUploadAlert(error.message || 'Gagal memuat detail tugas.');
        submitButton.disabled = true;
    }
}

async function loadExistingProof() {
    try {
        const payload = await PetugasAPI.request(`/officer/tasks/${taskId}/completion-proof`);
        existingProof = payload.data?.proof || null;
        if (existingProof?.photo_after) {
            photoPreview.src = PetugasAuth.resolveFileUrl(existingProof.photo_after);
            photoPreview.classList.remove('hidden');
        }
        if (existingProof?.notes) {
            notesInput.value = existingProof.notes;
        }
        submitButton.innerHTML = editButtonContent;
    } catch (error) {
        existingProof = null;
        console.warn('[UploadProof] bukti belum ada', error);
    }
}

photoInput?.addEventListener('change', () => {
    const file = photoInput.files[0];
    if (!file) {
        photoPreview.classList.add('hidden');
        return;
    }
    const reader = new FileReader();
    reader.onload = (e) => {
        photoPreview.src = e.target.result;
        photoPreview.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

function validateForm() {
    const errors = [];
    const note = notesInput.value.trim();
    if (!note) errors.push('Catatan penyelesaian wajib diisi.');
    if (!photoInput.files[0] && !existingProof?.photo_after) {
        errors.push('Foto bukti penyelesaian wajib diunggah.');
    }
    return errors;
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearUploadAlert();

    const errors = validateForm();
    if (errors.length > 0) {
        showUploadAlert(errors.join(' '));
        return;
    }

    const formData = new FormData();
    formData.append('note', notesInput.value.trim());
    if (photoInput.files[0]) {
        formData.append('photo_after', photoInput.files[0]);
    }

    const isEdit = currentTask?.status === 'menunggu_validasi_admin';
    const endpoint = isEdit ? `/officer/tasks/${taskId}/completion-proof` : `/officer/tasks/${taskId}/complete`;
    const method = isEdit ? 'PUT' : 'POST';

    submitButton.disabled = true;
    submitButton.innerText = isEdit ? 'Menyimpan...' : 'Mengirim...';

    try {
        const payload = await PetugasAPI.request(endpoint, {
            method,
            formData
        });
        showUploadAlert(payload.message || 'Bukti berhasil dikirim.', 'success');
        setTimeout(() => {
            window.location.href = `petugas-task-detail.php?id=${taskId}`;
        }, 1200);
    } catch (error) {
        console.error('[UploadProof] submit error', error);
        showUploadAlert(error.message || 'Gagal mengirim bukti.');
    } finally {
        submitButton.disabled = false;
        submitButton.innerHTML = currentTask?.status === 'menunggu_validasi_admin'
            ? editButtonContent
            : defaultButtonContent;
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadTaskDetail();
});

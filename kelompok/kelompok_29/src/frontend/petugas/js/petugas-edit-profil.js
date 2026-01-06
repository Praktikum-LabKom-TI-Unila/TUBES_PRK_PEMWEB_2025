const formProfil = document.getElementById('formEditProfilePetugas');
const inputNama = document.getElementById('nama');
const inputEmail = document.getElementById('email');
const inputTelepon = document.getElementById('telepon');
const inputDepartemen = document.getElementById('departemen');
const inputEmployeeId = document.getElementById('employeeId');
const fotoInput = document.getElementById('fotoProfilInput');
const fotoPreview = document.getElementById('fotoPreview');
const fotoIcon = document.getElementById('fotoIconPlaceholder');
const alertBox = document.getElementById('profilAlert');
const loadingBox = document.getElementById('profilLoading');
const btnSimpan = document.getElementById('btnSimpanProfil');
const profilNamaText = document.getElementById('profilNamaText');
const profilDepartemenText = document.getElementById('profilDepartemenText');
const profilUserId = document.getElementById('profilUserId');
const profilRole = document.getElementById('profilRole');
const profilJoinDate = document.getElementById('profilJoinDate');

let currentProfile = null;

function showProfilAlert(message, type = 'error') {
    if (!alertBox) return;
    alertBox.textContent = message;
    alertBox.classList.remove('hidden', 'bg-red-50', 'text-red-700', 'border-red-200', 'bg-green-50', 'text-green-700', 'border-green-200');
    if (type === 'success') {
        alertBox.classList.add('bg-green-50', 'text-green-700', 'border', 'border-green-200');
    } else {
        alertBox.classList.add('bg-red-50', 'text-red-700', 'border', 'border-red-200');
    }
}

function clearProfilAlert() {
    if (!alertBox) return;
    alertBox.classList.add('hidden');
    alertBox.textContent = '';
}

function fillProfileForm(profile) {
    inputNama.value = profile.full_name || '';
    inputEmail.value = profile.email || '';
    inputTelepon.value = profile.phone || '';
    inputDepartemen.value = profile.department || '';
    inputEmployeeId.value = profile.employee_id || '';
    profilNamaText.textContent = profile.full_name || 'Petugas Lapangan';
    profilDepartemenText.textContent = profile.department || '-';
    profilUserId.textContent = profile.user_id || '-';
    profilRole.textContent = profile.role || 'Petugas Lapangan';
    profilJoinDate.textContent = profile.joined_at
        ? new Date(profile.joined_at).toLocaleDateString('id-ID')
        : '-';

    const photo = profile.profile_photo ? PetugasAuth.resolveFileUrl(profile.profile_photo) : '';
    if (photo) {
        fotoPreview.src = photo;
        fotoPreview.classList.remove('hidden');
        fotoIcon?.classList.add('hidden');
    } else {
        fotoPreview.src = '';
        fotoPreview.classList.add('hidden');
        fotoIcon?.classList.remove('hidden');
    }
}

async function loadProfile() {
    try {
        const payload = await PetugasAPI.request('/officer/profile');
        currentProfile = payload.data || {};
        fillProfileForm(currentProfile);
    } catch (error) {
        console.error('[Profil] load error', error);
        showProfilAlert(error.message || 'Gagal memuat profil.');
    } finally {
        loadingBox?.classList.add('hidden');
    }
}

function validateForm() {
    const errors = [];
    if (!inputNama.value.trim()) errors.push('Nama lengkap wajib diisi.');
    if (!inputEmail.value.trim()) errors.push('Email wajib diisi.');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (inputEmail.value && !emailRegex.test(inputEmail.value)) errors.push('Format email tidak valid.');
    const telpDigits = inputTelepon.value.replace(/\D/g, '');
    if (telpDigits.length < 10) errors.push('Nomor telepon minimal 10 digit.');
    if (!inputDepartemen.value) errors.push('Departemen wajib dipilih.');
    return errors;
}

fotoInput?.addEventListener('change', () => {
    const file = fotoInput.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = (e) => {
        fotoPreview.src = e.target.result;
        fotoPreview.classList.remove('hidden');
        fotoIcon?.classList.add('hidden');
    };
    reader.readAsDataURL(file);
});

formProfil.addEventListener('submit', async (event) => {
    event.preventDefault();
    clearProfilAlert();

    const errors = validateForm();
    if (errors.length > 0) {
        showProfilAlert(errors.join(' '));
        return;
    }

    const formData = new FormData();
    formData.append('full_name', inputNama.value.trim());
    formData.append('email', inputEmail.value.trim());
    formData.append('phone', inputTelepon.value.trim());
    formData.append('department', inputDepartemen.value);
    if (currentProfile?.specialization) {
        formData.append('specialization', currentProfile.specialization);
    }
    if (fotoInput.files[0]) {
        formData.append('photo', fotoInput.files[0]);
    }

    btnSimpan.disabled = true;
    btnSimpan.innerText = 'Menyimpan...';

    try {
        const payload = await PetugasAPI.request('/officer/profile', {
            method: 'PUT',
            formData
        });
        currentProfile = payload.data || currentProfile;
        fillProfileForm(currentProfile);
        showProfilAlert(payload.message || 'Profil berhasil diperbarui.', 'success');
    } catch (error) {
        console.error('[Profil] update error', error);
        showProfilAlert(error.message || 'Gagal memperbarui profil.');
    } finally {
        btnSimpan.disabled = false;
        btnSimpan.innerText = 'Simpan Perubahan';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    PetugasAuth.requireOfficer();
    loadProfile();
});

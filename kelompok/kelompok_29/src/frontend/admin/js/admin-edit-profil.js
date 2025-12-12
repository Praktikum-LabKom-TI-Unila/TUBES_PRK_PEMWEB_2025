'use strict';

(function () {
  const form = document.getElementById('formEditProfile');
  if (!form) return;

  const DOM = {
    alert: document.getElementById('profileAlert'),
    name: document.getElementById('nama'),
    email: document.getElementById('email'),
    phone: document.getElementById('telepon'),
    address: document.getElementById('alamat'),
    department: document.getElementById('departemen'),
    employeeId: document.getElementById('employeeId'),
    userIdLabel: document.getElementById('profileUserId'),
    roleLabel: document.getElementById('profileRole'),
    profileDepartment: document.getElementById('profileDepartment'),
    joinedLabel: document.getElementById('profileJoined'),
    submit: form.querySelector('button[type="submit"]'),
    photoInput: document.getElementById('fotoProfil'),
    photoPreview: document.getElementById('profilePhotoPreview'),
    photoPlaceholder: document.getElementById('profilePhotoPlaceholder'),
  };

  let profile = null;
  let currentPhotoUrl = '';

  document.addEventListener('DOMContentLoaded', () => {
    bindPhotoSelection();
    loadProfile();
    form.addEventListener('submit', handleSubmit);
  });

  const bindPhotoSelection = () => {
    if (!DOM.photoInput) return;
    DOM.photoInput.addEventListener('change', handlePhotoSelection);
  };

  const showError = (message) => {
    AdminAPI.showInlineAlert(DOM.alert, message, 'error');
  };

  const clearAlert = () => {
    AdminAPI.showInlineAlert(DOM.alert);
  };

  // Ambil profil admin saat halaman dimuat dan isi form.
  const loadProfile = async () => {
    AdminAPI.setButtonLoading(DOM.submit, true, 'Memuat profil...');
    AdminAPI.showInlineAlert(DOM.alert, 'Memuat data profil admin...', 'info');
    try {
      profile = await AdminAPI.request('/api/admin/profile');
      applyProfileToForm();
      AdminAPI.showInlineAlert(DOM.alert);
    } catch (error) {
      console.error('[AdminProfile] load error', error);
      showError(error?.message || 'Gagal memuat profil admin.');
    } finally {
      AdminAPI.setButtonLoading(DOM.submit, false);
    }
  };

  const applyProfileToForm = () => {
    if (!profile) return;
    DOM.name.value = profile.full_name || '';
    DOM.email.value = profile.email || '';
    DOM.phone.value = profile.phone || '';
    DOM.address.value = profile.address || '';
    DOM.department.value = profile.department || '';
    DOM.employeeId.value = profile.employee_id || '-';
    DOM.userIdLabel.textContent = profile.id ?? '-';
    DOM.roleLabel.textContent = (profile.role || 'admin').toUpperCase();
    DOM.profileDepartment.textContent = profile.department || '-';
    const joinedSource = profile.joined_at || profile.created_at;
    DOM.joinedLabel.textContent = joinedSource ? AdminAPI.formatDate(joinedSource) : '-';
    currentPhotoUrl = profile.profile_photo || '';
    updatePhotoPreview(currentPhotoUrl);
  };

  const updatePhotoPreview = (source) => {
    if (!DOM.photoPreview || !DOM.photoPlaceholder) return;
    if (source) {
      DOM.photoPreview.src = source;
      DOM.photoPreview.classList.remove('hidden');
      DOM.photoPlaceholder.classList.add('hidden');
    } else {
      DOM.photoPreview.src = '';
      DOM.photoPreview.classList.add('hidden');
      DOM.photoPlaceholder.classList.remove('hidden');
    }
  };

  // Tangani perubahan file agar preview langsung terlihat.
  const handlePhotoSelection = () => {
    if (!DOM.photoInput) return;
    const file = DOM.photoInput.files?.[0];
    if (!file) {
      updatePhotoPreview(currentPhotoUrl);
      return;
    }

    if (!file.type.startsWith('image/')) {
      showError('File foto harus berupa gambar (JPG/PNG).');
      DOM.photoInput.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = () => {
      updatePhotoPreview(reader.result || '');
    };
    reader.readAsDataURL(file);
  };

  const validateInputs = () => {
    const fullName = DOM.name.value.trim();
    const email = DOM.email.value.trim();
    const phone = DOM.phone.value.trim();
    const department = DOM.department.value.trim();
    const address = DOM.address.value.trim();

    if (!fullName || !email || !phone || !department) {
      return { valid: false, message: 'Nama, email, telepon, dan departemen wajib diisi.' };
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
      return { valid: false, message: 'Format email tidak valid.' };
    }

    if (!/^\d+$/.test(phone) || phone.length < 10) {
      return { valid: false, message: 'Nomor telepon harus minimal 10 digit angka tanpa karakter lain.' };
    }

    return {
      valid: true,
      values: {
        full_name: fullName,
        email,
        phone,
        department,
        address,
      },
    };
  };

  const buildFormData = (values) => {
    const formData = new FormData();
    formData.append('full_name', values.full_name);
    formData.append('email', values.email);
    formData.append('phone', values.phone);
    formData.append('department', values.department);
    if (values.address) {
      formData.append('address', values.address);
    }
    const file = DOM.photoInput?.files?.[0];
    if (file) {
      formData.append('photo', file);
    }
    return formData;
  };

  const clearFileInput = () => {
    if (!DOM.photoInput) return;
    DOM.photoInput.value = '';
  };

  // Kirim perubahan profil (termasuk foto) ke API.
  const handleSubmit = async (event) => {
    event.preventDefault();
    clearAlert();
    const { valid, message, values } = validateInputs();
    if (!valid) {
      showError(message);
      return;
    }

    const payload = buildFormData(values);
    try {
      AdminAPI.setButtonLoading(DOM.submit, true, 'Menyimpan...');
      const updatedProfile = await AdminAPI.request('/api/admin/profile/update', {
        method: 'PUT',
        body: payload,
      });
      profile = updatedProfile;
      currentPhotoUrl = profile.profile_photo || '';
      applyProfileToForm();
      clearFileInput();
      AdminAPI.updateStoredAdminProfile(profile);
      const successMessage = 'Profil admin berhasil diperbarui.';
      AdminAPI.showInlineAlert(DOM.alert, successMessage, 'success');
      AdminAPI.showToast(successMessage, 'success');
    } catch (error) {
      console.error('[AdminProfile] update error', error);
      showError(error?.message || 'Gagal menyimpan perubahan profil.');
      updatePhotoPreview(currentPhotoUrl);
    } finally {
      AdminAPI.setButtonLoading(DOM.submit, false);
    }
  };

})();

'use strict';

(function () {
  const form = document.getElementById('formPetugasBaru');
  if (!form) return;

  const DOM = {
    alert: document.getElementById('formAlert'),
    submit: form.querySelector('button[type="submit"]'),
    specialization: document.getElementById('spesialisasi'),
    status: document.getElementById('statusPetugas'),
  };

  const API_ENDPOINT = '/api/admin/officers/create';
  const MIN_PASSWORD_LENGTH = 8;
  // Daftar nilai spesialisasi yang diizinkan oleh API-A5-01.
  const ALLOWED_SPECIALIZATIONS = [
    'Jalan_Raya',
    'Penerangan_Jalan',
    'Drainase',
    'Trotoar',
    'Taman',
    'Jembatan',
    'Rambu_Lalu_Lintas',
    'Fasilitas_Umum',
    'Lainnya',
  ];

  const sanitize = (value) => (typeof value === 'string' ? value.trim() : '');

  const maskSensitiveValues = (values = {}) => {
    const clone = { ...values };
    if (clone.password) clone.password = '[hidden]';
    if (clone.confirm_password) clone.confirm_password = '[hidden]';
    return clone;
  };

  const showError = (message) => {
    AdminAPI.showInlineAlert(DOM.alert, message, 'error');
  };

  const clearAlert = () => {
    AdminAPI.showInlineAlert(DOM.alert);
  };

  const isValidEmail = (value) => {
    if (!value) return false;
    const simplePattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return simplePattern.test(value);
  };

  // Map status UI (aktif/nonaktif) ke enumerasi status API (tersedia/sibuk).
  const mapStatusValue = (status) => {
    const normalized = sanitize(status).toLowerCase();
    if (normalized === 'aktif' || normalized === 'tersedia') return 'tersedia';
    if (normalized === 'nonaktif' || normalized === 'sibuk') return 'sibuk';
    return '';
  };

  const requestCreateOfficer = async (payload) => {
    console.info('[AdminCreateOfficer] 🚀 Mengirim payload', maskSensitiveValues(payload));
    const response = await AdminAPI.request(API_ENDPOINT, {
      method: 'POST',
      body: JSON.stringify(payload),
    });
    console.info('[AdminCreateOfficer] 📬 Respons API', response);
    return response;
  };

  const collectFormValues = () => {
    const formData = new FormData(form);
    const raw = Object.fromEntries(formData.entries());
    return {
      full_name: sanitize(raw.full_name),
      email: sanitize(raw.email),
      phone: sanitize(raw.phone),
      address: sanitize(raw.address),
      department: sanitize(raw.department),
      specialization: sanitize(raw.specialization),
      officer_status: sanitize(raw.officer_status),
      password: raw.password || '',
      confirm_password: raw.confirm_password || '',
      employee_id: sanitize(raw.employee_id),
    };
  };

  const validateValues = (values) => {
    const requiredFields = [
      ['full_name', 'Nama lengkap wajib diisi.'],
      ['email', 'Email wajib diisi.'],
      ['phone', 'Nomor telepon wajib diisi.'],
      ['address', 'Alamat wajib diisi.'],
      ['department', 'Departemen wajib diisi.'],
      ['specialization', 'Spesialisasi petugas wajib dipilih.'],
      ['officer_status', 'Status petugas wajib dipilih.'],
      ['password', 'Password wajib diisi.'],
      ['confirm_password', 'Konfirmasi password wajib diisi.'],
    ];

    for (const [field, message] of requiredFields) {
      if (!values[field]) {
        throw new Error(message);
      }
    }

    if (!isValidEmail(values.email)) {
      throw new Error('Format email tidak valid.');
    }

    if (values.password.length < MIN_PASSWORD_LENGTH) {
      throw new Error(`Password minimal ${MIN_PASSWORD_LENGTH} karakter.`);
    }

    if (values.password !== values.confirm_password) {
      throw new Error('Konfirmasi password harus sama.');
    }

    if (!ALLOWED_SPECIALIZATIONS.includes(values.specialization)) {
      throw new Error('Pilih spesialisasi petugas yang valid.');
    }

    const normalizedStatus = mapStatusValue(values.officer_status);
    if (!normalizedStatus) {
      throw new Error('Status petugas hanya boleh aktif (tersedia) atau nonaktif (sibuk).');
    }

    return { ...values, officer_status: normalizedStatus };
  };

  const buildPayload = (values) => {
    const payload = {
      full_name: values.full_name,
      email: values.email,
      password: values.password,
      phone: values.phone,
      address: values.address,
      specialization: values.specialization,
      department: values.department,
      officer_status: values.officer_status,
    };

    if (values.employee_id) {
      payload.employee_id = values.employee_id;
    }

    return payload;
  };

  const getFriendlyError = (error) => {
    if (error?.isNetworkError) {
      return error.message;
    }
    const message = error?.message || '';
    if (!message || /Failed to fetch|NetworkError/i.test(message)) {
      return 'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.';
    }
    return message;
  };

  const resetFormState = () => {
    form.reset();
    if (DOM.specialization) DOM.specialization.value = '';
    if (DOM.status) DOM.status.value = '';
  };

  const handleSubmit = async (event) => {
    event.preventDefault();
    event.stopPropagation();
    clearAlert();

    let payload;
    try {
      const values = collectFormValues();
      const validatedValues = validateValues(values);
      payload = buildPayload(validatedValues);
    } catch (validationError) {
      showError(validationError?.message || 'Form belum lengkap.');
      return;
    }

    try {
      AdminAPI.setButtonLoading(DOM.submit, true, 'Membuat akun...');
      const responsePayload = await requestCreateOfficer(payload);

      console.info('[AdminCreateOfficer] ✅ Berhasil membuat petugas', responsePayload?.data);
      resetFormState();
      const successMessage = responsePayload?.message || 'Akun petugas berhasil dibuat.';
      AdminAPI.showInlineAlert(DOM.alert, successMessage, 'success');
      AdminAPI.showToast(successMessage, 'success');
    } catch (error) {
      console.error('[AdminCreateOfficer] request error', error);
      showError(getFriendlyError(error));
    } finally {
      AdminAPI.setButtonLoading(DOM.submit, false);
    }
  };

  form.addEventListener('submit', handleSubmit);
})();

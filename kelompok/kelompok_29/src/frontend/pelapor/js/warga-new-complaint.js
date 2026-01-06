const DOMComplaint = {
    form: document.getElementById('newComplaintForm'),
    alert: document.getElementById('newComplaintAlert'),
    categorySelect: document.getElementById('categorySelect'),
    titleInput: document.getElementById('titleInput'),
    description: document.getElementById('description'),
    locationInput: document.getElementById('locationInput'),
    latitudeInput: document.getElementById('latitudeInput'),
    longitudeInput: document.getElementById('longitudeInput'),
    photoInput: document.getElementById('photoUpload'),
    uploadLabel: document.getElementById('upload-label'),
    imagePreviewArea: document.getElementById('image-preview-area'),
    submitButton: document.querySelector('#newComplaintForm button[type="submit"]')
};

let mapInstance;
let mapMarker;
let selectedPhoto = null;

function showFormAlert(message = '', type = 'error') {
    if (!DOMComplaint.alert) return;
    if (!message) {
        DOMComplaint.alert.classList.add('hidden');
        DOMComplaint.alert.textContent = '';
        return;
    }
    const palette =
        type === 'success'
            ? 'bg-green-50 border border-green-200 text-green-700'
            : 'bg-red-50 border border-red-200 text-red-700';
    DOMComplaint.alert.className = `p-3 rounded-lg text-sm ${palette}`;
    DOMComplaint.alert.textContent = message;
    DOMComplaint.alert.classList.remove('hidden');
}

function handlePhotoChange(event) {
    const file = event.target.files[0];
    if (!file) {
        DOMComplaint.imagePreviewArea.innerHTML = '';
        DOMComplaint.imagePreviewArea.classList.add('hidden');
        DOMComplaint.uploadLabel.classList.remove('hidden');
        selectedPhoto = null;
        return;
    }
    if (file.size > 5 * 1024 * 1024) {
        showFormAlert('Ukuran foto maksimal 5MB.');
        event.target.value = '';
        return;
    }
    selectedPhoto = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        DOMComplaint.uploadLabel.classList.add('hidden');
        DOMComplaint.imagePreviewArea.classList.remove('hidden');
        DOMComplaint.imagePreviewArea.innerHTML = `
            <div class="relative w-full h-64 border border-gray-300 rounded-lg overflow-hidden mb-4">
                <img src="${e.target.result}" alt="Foto Bukti" class="w-full h-full object-cover">
                <button type="button" id="remove-photo-btn" class="absolute top-2 right-2 bg-red-600 text-white rounded-full p-1 shadow-lg hover:bg-red-700 transition-colors">
                    <span class="material-icons text-base">close</span>
                </button>
            </div>
        `;
        document.getElementById('remove-photo-btn').addEventListener('click', () => {
            DOMComplaint.photoInput.value = '';
            selectedPhoto = null;
            DOMComplaint.imagePreviewArea.innerHTML = '';
            DOMComplaint.imagePreviewArea.classList.add('hidden');
            DOMComplaint.uploadLabel.classList.remove('hidden');
        });
    };
    reader.readAsDataURL(file);
}

async function loadCategories() {
    try {
        const payload = await PelaporAPI.request('/complaints/categories');
        const records = payload?.data?.records || [];
        DOMComplaint.categorySelect.innerHTML = '<option value="">Pilih kategori pengaduan</option>';
        records.forEach((record) => {
            const option = document.createElement('option');
            option.value = record.id;
            option.textContent = record.label;
            DOMComplaint.categorySelect.appendChild(option);
        });
    } catch (error) {
        console.error('[NewComplaint] categories error', error);
        DOMComplaint.categorySelect.innerHTML = '<option value="">Gagal memuat kategori</option>';
        showFormAlert(error.message || 'Kategori gagal dimuat.');
    }
}

function initMap() {
    const mapContainer = document.getElementById('complaintMap');
    if (!mapContainer) return;
    mapInstance = L.map('complaintMap').setView([-6.2, 106.816], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
    }).addTo(mapInstance);

    mapInstance.on('click', async (e) => {
        setLocation(e.latlng.lat, e.latlng.lng);
        await fillAddressFromCoords(e.latlng.lat, e.latlng.lng);
    });
}

function setLocation(lat, lng) {
    if (!DOMComplaint.latitudeInput || !DOMComplaint.longitudeInput) return;
    DOMComplaint.latitudeInput.value = lat?.toFixed(6) || '';
    DOMComplaint.longitudeInput.value = lng?.toFixed(6) || '';
    if (!mapInstance) return;
    if (!mapMarker) {
        mapMarker = L.marker([lat, lng]).addTo(mapInstance);
    } else {
        mapMarker.setLatLng([lat, lng]);
    }
    mapInstance.setView([lat, lng], 15);
}

async function fillAddressFromCoords(lat, lng) {
    try {
        const response = await fetch(
            `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&accept-language=id`
        );
        const data = await response.json();
        if (data?.display_name && DOMComplaint.locationInput) {
            DOMComplaint.locationInput.value = data.display_name;
        }
    } catch (error) {
        console.warn('[NewComplaint] reverse geocode gagal', error);
    }
}

async function handleSubmit(event) {
    event.preventDefault();
    showFormAlert('');

    const category = DOMComplaint.categorySelect.value;
    const title = DOMComplaint.titleInput.value.trim();
    const description = DOMComplaint.description.value.trim();
    const address = DOMComplaint.locationInput.value.trim();
    if (!selectedPhoto) {
        showFormAlert('Foto bukti wajib diunggah.');
        return;
    }
    if (!category || !title || !description) {
        showFormAlert('Kategori, judul, dan deskripsi wajib diisi.');
        return;
    }
    if (!address) {
        showFormAlert('Alamat wajib diisi. Pilih lokasi di peta atau tulis secara manual.');
        return;
    }

    DOMComplaint.submitButton.disabled = true;
    DOMComplaint.submitButton.innerHTML =
        '<span class="flex items-center gap-2"><i class="material-icons animate-spin text-base">autorenew</i> Mengirim...</span>';

    const payload = new FormData();
    payload.append('category', category);
    payload.append('title', title);
    payload.append('description', description);
    payload.append('address', address);
    const latValue = DOMComplaint.latitudeInput.value;
    const lngValue = DOMComplaint.longitudeInput.value;
    if (latValue && lngValue) {
        payload.append('latitude', latValue);
        payload.append('longitude', lngValue);
    }
    payload.append('photo', selectedPhoto);

    try {
        const response = await PelaporAPI.request('/complaints', {
            method: 'POST',
            formData: payload
        });
        showFormAlert(response?.message || 'Pengaduan berhasil dikirim.', 'success');
        setTimeout(() => {
            window.location.href = 'warga-dashboard.php';
        }, 1500);
    } catch (error) {
        console.error('[NewComplaint] error', error);
        showFormAlert(error.message || 'Pengaduan gagal dikirim.');
    } finally {
        DOMComplaint.submitButton.disabled = false;
        DOMComplaint.submitButton.innerHTML =
            '<i class="material-icons text-xl">description</i> Kirim Pengaduan';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    PelaporAuth.requirePelapor();
    loadCategories();
    initMap();
    DOMComplaint.photoInput?.addEventListener('change', handlePhotoChange);
    DOMComplaint.form?.addEventListener('submit', handleSubmit);
});

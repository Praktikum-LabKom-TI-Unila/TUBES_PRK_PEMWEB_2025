/**
 * Profile Management Module
 * Handles user profile updates and password changes
 */

const Profile = (function() {
    const originalFormData = {};

    function init() {
        // Store original form data
        originalFormData.name = document.getElementById('name')?.value;
        originalFormData.email = document.getElementById('email')?.value;
        originalFormData.phone = document.getElementById('phone')?.value;
        
        // Setup avatar upload
        const avatarInput = document.getElementById('avatarInput');
        if (avatarInput) {
            avatarInput.addEventListener('change', handleAvatarUpload);
        }
    }

    async function handleAvatarUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Validate file type
        if (!file.type.startsWith('image/')) {
            showToast('error', 'Gagal', 'File harus berupa gambar');
            return;
        }

        // Validate file size (max 2MB)
        if (file.size > 2 * 1024 * 1024) {
            showToast('error', 'Gagal', 'Ukuran file maksimal 2MB');
            return;
        }

        const formData = new FormData();
        formData.append('avatar', file);

        try {
            const response = await fetch('/api/profile/upload-avatar', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', 'Berhasil', 'Foto profil berhasil diupload');
                
                // Update all avatar displays
                const avatarUrl = result.data.avatar_url;
                updateAvatarDisplay(avatarUrl);
                
                // Reload after 1.5 seconds
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', 'Gagal', result.message || 'Gagal mengupload foto');
            }
        } catch (error) {
            console.error('Error uploading avatar:', error);
            showToast('error', 'Gagal', 'Terjadi kesalahan saat mengupload foto');
        }
    }

    function updateAvatarDisplay(avatarUrl) {
        // Update header avatar
        const headerAvatar = document.getElementById('headerAvatar');
        if (headerAvatar) {
            if (headerAvatar.tagName === 'IMG') {
                headerAvatar.src = avatarUrl;
            } else {
                const img = document.createElement('img');
                img.id = 'headerAvatar';
                img.src = avatarUrl;
                img.alt = 'Avatar';
                img.className = 'w-24 h-24 rounded-full object-cover shadow-lg border-4 border-white';
                headerAvatar.parentNode.replaceChild(img, headerAvatar);
            }
        }

        // Update profile avatar
        const profileAvatar = document.getElementById('profileAvatar');
        if (profileAvatar) {
            if (profileAvatar.tagName === 'IMG') {
                profileAvatar.src = avatarUrl;
            } else {
                const img = document.createElement('img');
                img.id = 'profileAvatar';
                img.src = avatarUrl;
                img.alt = 'Avatar';
                img.className = 'w-20 h-20 rounded-full object-cover border-2 border-slate-300';
                profileAvatar.parentNode.replaceChild(img, profileAvatar);
            }
        }
    }

    async function updateProfile() {
        const name = document.getElementById('name')?.value.trim();
        const email = document.getElementById('email')?.value.trim();
        const phone = document.getElementById('phone')?.value.trim();

        // Validation
        if (!name) {
            showToast('error', 'Gagal', 'Nama tidak boleh kosong');
            return;
        }

        if (!email) {
            showToast('error', 'Gagal', 'Email tidak boleh kosong');
            return;
        }

        if (!isValidEmail(email)) {
            showToast('error', 'Gagal', 'Format email tidak valid');
            return;
        }

        try {
            const response = await fetch('/api/profile/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    name,
                    email,
                    phone
                })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', 'Berhasil', result.message || 'Profil berhasil diperbarui');
                
                // Update original data
                originalFormData.name = name;
                originalFormData.email = email;
                originalFormData.phone = phone;
                
                // Reload page after 1.5 seconds to update header
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('error', 'Gagal', result.message || 'Gagal memperbarui profil');
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            showToast('error', 'Gagal', 'Terjadi kesalahan saat memperbarui profil');
        }
    }

    async function updatePassword() {
        const oldPassword = document.getElementById('oldPassword')?.value;
        const newPassword = document.getElementById('newPassword')?.value;
        const confirmPassword = document.getElementById('confirmPassword')?.value;

        // Validation
        if (!oldPassword) {
            showToast('error', 'Gagal', 'Password lama tidak boleh kosong');
            return;
        }

        if (!newPassword) {
            showToast('error', 'Gagal', 'Password baru tidak boleh kosong');
            return;
        }

        if (newPassword.length < 6) {
            showToast('error', 'Gagal', 'Password baru minimal 6 karakter');
            return;
        }

        if (newPassword !== confirmPassword) {
            showToast('error', 'Gagal', 'Konfirmasi password tidak cocok');
            return;
        }

        if (oldPassword === newPassword) {
            showToast('error', 'Gagal', 'Password baru harus berbeda dengan password lama');
            return;
        }

        try {
            const response = await fetch('/api/profile/change-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    old_password: oldPassword,
                    new_password: newPassword,
                    confirm_password: confirmPassword
                })
            });

            const result = await response.json();

            if (result.success) {
                showToast('success', 'Berhasil', result.message || 'Password berhasil diubah');
                
                // Reset form
                document.getElementById('passwordForm').reset();
            } else {
                showToast('error', 'Gagal', result.message || 'Gagal mengubah password');
            }
        } catch (error) {
            console.error('Error updating password:', error);
            showToast('error', 'Gagal', 'Terjadi kesalahan saat mengubah password');
        }
    }

    function resetForm() {
        document.getElementById('name').value = originalFormData.name || '';
        document.getElementById('email').value = originalFormData.email || '';
        document.getElementById('phone').value = originalFormData.phone || '';
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showToast(type, title, message) {
        const toast = document.getElementById('toast');
        const icon = document.getElementById('toastIcon');
        const titleEl = document.getElementById('toastTitle');
        const messageEl = document.getElementById('toastMessage');

        const icons = {
            success: '<svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
            error: '<svg class="w-6 h-6 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
            warning: '<svg class="w-6 h-6 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
        };

        icon.innerHTML = icons[type] || icons.error;
        titleEl.textContent = title;
        messageEl.textContent = message;

        toast.classList.remove('hidden');
        setTimeout(() => hideToast(), 5000);
    }

    function hideToast() {
        document.getElementById('toast').classList.add('hidden');
    }

    // Initialize on DOM load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    return {
        updateProfile,
        updatePassword,
        resetForm,
        hideToast
    };
})();

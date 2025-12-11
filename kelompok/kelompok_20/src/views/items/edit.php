
<div class="min-h-screen gradient-mesh py-12 px-4">
    <div class="container mx-auto max-w-3xl">
        
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-3">
                Edit Laporan
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                Perbarui informasi barang yang hilang atau ditemukan
            </p>
        </div>

        
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200/50 dark:border-slate-700/50 p-6 md:p-8">
            <form 
                action="<?= base_url('index.php?page=items&action=update') ?>" 
                method="POST" 
                enctype="multipart/form-data"
                class="space-y-6"
            >
                
                <input type="hidden" name="id" value="<?= $item['id'] ?>">

                
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Judul Barang <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        value="<?= htmlspecialchars($item['title']) ?>"
                        placeholder="Contoh: Dompet Kulit Coklat, Kunci Motor Honda, dll"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    >
                </div>

                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                        Jenis Laporan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <label class="relative cursor-pointer group">
                            <input 
                                type="radio" 
                                name="type" 
                                value="lost" 
                                required
                                <?= $item['type'] === 'lost' ? 'checked' : '' ?>
                                class="peer sr-only"
                            >
                            <div class="p-6 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 rounded-xl transition-all peer-checked:border-rose-500 peer-checked:bg-rose-50 dark:peer-checked:bg-rose-950/30 peer-checked:shadow-lg peer-checked:shadow-rose-500/20 hover:border-rose-300 dark:hover:border-rose-700">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-12 h-12 bg-rose-100 dark:bg-rose-900/50 rounded-xl flex items-center justify-center peer-checked:bg-rose-500 transition-colors">
                                        <svg class="w-6 h-6 text-rose-600 dark:text-rose-400 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">Kehilangan</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Saya kehilangan barang</p>
                                    </div>
                                </div>
                            </div>
                        </label>

                        
                        <label class="relative cursor-pointer group">
                            <input 
                                type="radio" 
                                name="type" 
                                value="found" 
                                required
                                <?= $item['type'] === 'found' ? 'checked' : '' ?>
                                class="peer sr-only"
                            >
                            <div class="p-6 bg-white dark:bg-slate-900 border-2 border-slate-200 dark:border-slate-700 rounded-xl transition-all peer-checked:border-teal-500 peer-checked:bg-teal-50 dark:peer-checked:bg-teal-950/30 peer-checked:shadow-lg peer-checked:shadow-teal-500/20 hover:border-teal-300 dark:hover:border-teal-700">
                                <div class="flex items-center gap-3 mb-2">
                                    <div class="w-12 h-12 bg-teal-100 dark:bg-teal-900/50 rounded-xl flex items-center justify-center peer-checked:bg-teal-500 transition-colors">
                                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400 peer-checked:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 dark:text-white text-lg">Menemukan</h3>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">Saya menemukan barang</p>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Kategori <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="category_id" 
                            name="category_id" 
                            required
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all appearance-none cursor-pointer"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                        >
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" <?= $item['category_id'] == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    
                    <div>
                        <label for="location_id" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                            Lokasi Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <select 
                            id="location_id" 
                            name="location_id" 
                            required
                            class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all appearance-none cursor-pointer"
                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%236b7280%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%272%27 d=%27M19 9l-7 7-7-7%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                        >
                            <option value="">Pilih Lokasi</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= $location['id'] ?>" <?= $item['location_id'] == $location['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($location['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                
                <div>
                    <label for="incident_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="incident_date" 
                        name="incident_date" 
                        required
                        value="<?= $item['incident_date'] ?>"
                        max="<?= date('Y-m-d') ?>"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    >
                </div>

                
                <?php if (!empty($item['image_path'])): ?>
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl p-4 border border-slate-200 dark:border-slate-700">
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                        Foto Saat Ini
                    </label>
                    <div class="flex items-start gap-4">
                        <img 
                            src="<?= base_url($item['image_path']) ?>" 
                            alt="Current image"
                            class="w-32 h-32 object-cover rounded-lg shadow-md"
                            onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 24 24%27 stroke=%27%23cbd5e1%27%3E%3Cpath stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z%27/%3E%3C/svg%3E'"
                        >
                        <div class="flex-1">
                            <p class="text-sm text-slate-600 dark:text-slate-400 mb-2">
                                Upload foto baru jika ingin mengubah gambar
                            </p>
                            <label class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium cursor-pointer transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Ubah Foto
                                <input 
                                    type="file" 
                                    name="image" 
                                    accept="image/*"
                                    class="hidden"
                                    onchange="previewNewImage(event)"
                                >
                            </label>
                        </div>
                    </div>
                    <div id="new-image-preview" class="hidden mt-4 pt-4 border-t border-slate-200 dark:border-slate-700">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">Preview Foto Baru:</p>
                        <img id="preview-new-img" src="" alt="New preview" class="max-h-48 rounded-lg shadow-lg">
                    </div>
                </div>
                <?php else: ?>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Foto Barang
                    </label>
                    <div 
                        id="dropzone" 
                        class="relative border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-xl p-8 text-center hover:border-primary-500 dark:hover:border-primary-500 transition-all cursor-pointer bg-slate-50 dark:bg-slate-900/50"
                    >
                        <input 
                            type="file" 
                            id="image" 
                            name="image" 
                            accept="image/*"
                            class="hidden"
                            onchange="previewImage(event)"
                        >
                        <div id="upload-placeholder">
                            <svg class="w-12 h-12 mx-auto mb-4 text-slate-400 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-slate-600 dark:text-slate-400 mb-2">
                                <span class="font-semibold text-primary-600 dark:text-primary-400">Klik untuk upload</span> atau drag & drop
                            </p>
                            <p class="text-sm text-slate-500 dark:text-slate-500">PNG, JPG, JPEG (Max 5MB)</p>
                        </div>
                        <div id="image-preview" class="hidden">
                            <img id="preview-img" src="" alt="Preview" class="max-h-64 mx-auto rounded-lg shadow-lg">
                            <button 
                                type="button" 
                                onclick="removeImage()"
                                class="mt-4 px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition-colors text-sm font-medium"
                            >
                                Hapus Foto
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                
                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Deskripsi Detail <span class="text-rose-500">*</span>
                    </label>
                    <textarea 
                        id="description" 
                        name="description" 
                        required
                        rows="5"
                        placeholder="Jelaskan ciri-ciri barang, warna, merek, kondisi, dan detail lainnya yang membantu identifikasi..."
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none"
                    ><?= htmlspecialchars($item['description']) ?></textarea>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Semakin detail, semakin mudah barang ditemukan atau diklaim pemiliknya
                    </p>
                </div>

                
                <div class="bg-gradient-to-br from-primary-50 to-sky-50 dark:from-primary-950/20 dark:to-sky-950/20 rounded-xl p-6 border border-primary-200 dark:border-primary-800/30">
                    <div class="flex items-start gap-3 mb-4">
                        <input 
                            type="checkbox" 
                            id="is_safe_claim" 
                            name="is_safe_claim" 
                            value="1"
                            <?= !empty($item['is_safe_claim']) ? 'checked' : '' ?>
                            onchange="toggleSafeClaim()"
                            class="w-5 h-5 mt-0.5 text-primary-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-2 focus:ring-primary-500 cursor-pointer"
                        >
                        <div class="flex-1">
                            <label for="is_safe_claim" class="font-semibold text-slate-900 dark:text-white cursor-pointer">
                                Aktifkan Fitur Safe Claim
                            </label>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                Lindungi barang Anda dengan pertanyaan keamanan. Hanya yang bisa menjawab dengan benar yang dapat mengklaim barang ini.
                            </p>
                        </div>
                    </div>

                    
                    <div id="safe-claim-fields" class="<?= empty($item['is_safe_claim']) ? 'hidden' : '' ?> space-y-4 mt-4 pt-4 border-t border-primary-200 dark:border-primary-800/30">
                        <div>
                            <label for="security_question" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Pertanyaan Keamanan
                            </label>
                            <input 
                                type="text" 
                                id="security_question" 
                                name="security_question" 
                                value="<?= htmlspecialchars($item['security_question'] ?? '') ?>"
                                placeholder="Contoh: Apa warna casing HP ini? Merek jam tangan apa?"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                <?= !empty($item['is_safe_claim']) ? 'required' : '' ?>
                            >
                        </div>

                        <div>
                            <label for="security_answer" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Jawaban Kunci
                            </label>
                            <input 
                                type="text" 
                                id="security_answer" 
                                name="security_answer" 
                                value="<?= htmlspecialchars($item['security_answer'] ?? '') ?>"
                                placeholder="Jawaban yang benar (case-insensitive)"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                                <?= !empty($item['is_safe_claim']) ? 'required' : '' ?>
                            >
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                Tips: Gunakan jawaban yang spesifik dan sulit ditebak orang lain
                            </p>
                        </div>
                    </div>
                </div>

                
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3.5 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a 
                        href="<?= base_url('index.php?page=items&action=show&id=' . $item['id']) ?>"
                        class="flex-1 px-6 py-3.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        
        <div class="mt-6 bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/30 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-sky-900 dark:text-sky-200">
                    <p class="font-semibold mb-1">Perhatian:</p>
                    <ul class="list-disc list-inside space-y-1 text-sky-800 dark:text-sky-300">
                        <li>Pastikan informasi yang diubah tetap akurat</li>
                        <li>Foto baru akan menggantikan foto lama jika diupload</li>
                        <li>Safe Claim dapat diaktifkan atau dinonaktifkan sesuai kebutuhan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<script>

function previewNewImage(event) {
    const file = event.target.files[0];
    if (file) {

        if (file.size > 5 * 1024 * 1024) {
            alert('File terlalu besar! Maksimal 5MB.');
            event.target.value = '';
            return;
        }

        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar (PNG, JPG, JPEG)');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-new-img').src = e.target.result;
            document.getElementById('new-image-preview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function previewImage(event) {
    const file = event.target.files[0];
    if (file) {

        if (file.size > 5 * 1024 * 1024) {
            alert('File terlalu besar! Maksimal 5MB.');
            event.target.value = '';
            return;
        }

        if (!file.type.match('image.*')) {
            alert('File harus berupa gambar (PNG, JPG, JPEG)');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('upload-placeholder').classList.add('hidden');
            document.getElementById('image-preview').classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
}

function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('preview-img').src = '';
}

function toggleSafeClaim() {
    const checkbox = document.getElementById('is_safe_claim');
    const fields = document.getElementById('safe-claim-fields');
    const questionInput = document.getElementById('security_question');
    const answerInput = document.getElementById('security_answer');
    
    if (checkbox.checked) {
        fields.classList.remove('hidden');
        questionInput.setAttribute('required', 'required');
        answerInput.setAttribute('required', 'required');
    } else {
        fields.classList.add('hidden');
        questionInput.removeAttribute('required');
        answerInput.removeAttribute('required');
    }
}

const dropzone = document.getElementById('dropzone');
if (dropzone) {
    const fileInput = document.getElementById('image');
    
    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-950/30');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-950/30');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-950/30');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            previewImage({ target: fileInput });
        }
    });
}
</script>

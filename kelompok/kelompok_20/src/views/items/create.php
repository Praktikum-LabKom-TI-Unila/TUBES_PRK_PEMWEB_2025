<!-- Create Item Form -->
<div class="min-h-screen gradient-mesh py-12 px-4">
    <div class="container mx-auto max-w-3xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 dark:text-white mb-3">
                Buat Laporan Baru
            </h1>
            <p class="text-slate-600 dark:text-slate-400">
                Laporkan barang yang hilang atau ditemukan untuk membantu sesama civitas Unila
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-xl rounded-2xl shadow-xl border border-slate-200/50 dark:border-slate-700/50 p-6 md:p-8">
            <form 
                action="<?= base_url('index.php?page=items&action=store') ?>" 
                method="POST" 
                enctype="multipart/form-data"
                class="space-y-6"
            >
                <!-- Judul Barang -->
                <div>
                    <label for="title" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Judul Barang <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        required
                        placeholder="Contoh: Dompet Kulit Coklat, Kunci Motor Honda, dll"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Jenis Laporan (Radio Cards) -->
                <div>
                    <label class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                        Jenis Laporan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kehilangan Card -->
                        <label class="relative cursor-pointer group">
                            <input 
                                type="radio" 
                                name="type" 
                                value="lost" 
                                required
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

                        <!-- Menemukan Card -->
                        <label class="relative cursor-pointer group">
                            <input 
                                type="radio" 
                                name="type" 
                                value="found" 
                                required
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

                <!-- Kategori & Lokasi Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kategori -->
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
                                <option value="<?= $category['id'] ?>">
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Lokasi -->
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
                                <option value="<?= $location['id'] ?>">
                                    <?= htmlspecialchars($location['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Tanggal Kejadian -->
                <div>
                    <label for="incident_date" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                        Tanggal Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="incident_date" 
                        name="incident_date" 
                        required
                        max="<?= date('Y-m-d') ?>"
                        class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all"
                    >
                </div>

                <!-- Foto Barang (Drag & Drop) -->
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

                <!-- Deskripsi -->
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
                    ></textarea>
                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Semakin detail, semakin mudah barang ditemukan atau diklaim pemiliknya
                    </p>
                </div>

                <!-- Safe Claim Feature -->
                <div class="bg-gradient-to-br from-primary-50 to-sky-50 dark:from-primary-950/20 dark:to-sky-950/20 rounded-xl p-6 border border-primary-200 dark:border-primary-800/30">
                    <div class="flex items-start gap-3 mb-4">
                        <input 
                            type="checkbox" 
                            id="is_safe_claim" 
                            name="is_safe_claim" 
                            value="1"
                            onchange="toggleSafeClaim()"
                            class="w-5 h-5 mt-0.5 text-primary-600 bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 rounded focus:ring-2 focus:ring-primary-500 cursor-pointer"
                        >
                        <div class="flex-1">
                            <label for="is_safe_claim" class="font-semibold text-slate-900 dark:text-white cursor-pointer">
                                🔒 Aktifkan Fitur Safe Claim
                            </label>
                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-1">
                                Lindungi barang Anda dengan pertanyaan keamanan. Hanya yang bisa menjawab dengan benar yang dapat mengklaim barang ini.
                            </p>
                        </div>
                    </div>

                    <!-- Safe Claim Fields (Hidden by default) -->
                    <div id="safe-claim-fields" class="hidden space-y-4 mt-4 pt-4 border-t border-primary-200 dark:border-primary-800/30">
                        <div>
                            <label for="security_question" class="block text-sm font-semibold text-slate-700 dark:text-slate-300 mb-2">
                                Pertanyaan Keamanan
                            </label>
                            <input 
                                type="text" 
                                id="security_question" 
                                name="security_question" 
                                placeholder="Contoh: Apa warna casing HP ini? Merek jam tangan apa?"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
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
                                placeholder="Jawaban yang benar (case-insensitive)"
                                class="w-full px-4 py-3 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-600 rounded-xl text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-all"
                            >
                            <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                                💡 Tips: Gunakan jawaban yang spesifik dan sulit ditebak orang lain
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-4">
                    <button 
                        type="submit"
                        class="flex-1 px-6 py-3.5 gradient-primary text-white rounded-xl font-semibold shadow-lg shadow-primary-500/30 hover:shadow-xl hover:shadow-primary-500/40 hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Buat Laporan
                    </button>
                    <a 
                        href="<?= base_url('index.php?page=items') ?>"
                        class="flex-1 px-6 py-3.5 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-xl font-semibold hover:bg-slate-300 dark:hover:bg-slate-600 transition-all text-center"
                    >
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Card -->
        <div class="mt-6 bg-sky-50 dark:bg-sky-950/30 border border-sky-200 dark:border-sky-800/30 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-sky-600 dark:text-sky-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="text-sm text-sky-900 dark:text-sky-200">
                    <p class="font-semibold mb-1">Tips Membuat Laporan yang Efektif:</p>
                    <ul class="list-disc list-inside space-y-1 text-sky-800 dark:text-sky-300">
                        <li>Sertakan foto yang jelas untuk mempermudah identifikasi</li>
                        <li>Tuliskan deskripsi yang detail dan spesifik</li>
                        <li>Gunakan fitur Safe Claim untuk barang berharga</li>
                        <li>Pastikan lokasi dan tanggal kejadian akurat</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Image Preview and Safe Claim Toggle -->
<script>
// Image Preview Function
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('File terlalu besar! Maksimal 5MB.');
            event.target.value = '';
            return;
        }

        // Validate file type
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

// Remove Image Function
function removeImage() {
    document.getElementById('image').value = '';
    document.getElementById('upload-placeholder').classList.remove('hidden');
    document.getElementById('image-preview').classList.add('hidden');
    document.getElementById('preview-img').src = '';
}

// Toggle Safe Claim Fields
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
        questionInput.value = '';
        answerInput.value = '';
    }
}

// Drag and Drop for Image Upload
const dropzone = document.getElementById('dropzone');
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
</script>

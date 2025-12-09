<div class="max-w-2xl mx-auto">
    <div class="glass-effect p-8 rounded-3xl">
        <div class="mb-6">
            <h1 class="text-3xl font-bold flex items-center gap-3">
                <i data-lucide="plus-square" class="w-8 h-8"></i>
                Tambah Produk Baru
            </h1>
            <p class="text-gray-200 mt-2">Isi form di bawah untuk menambahkan produk</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="bg-red-500/20 text-red-200 p-4 mb-6 rounded-xl border border-red-400/40 flex items-start gap-3">
                <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0 mt-0.5"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div>
                <label class="block mb-2 font-semibold text-gray-200 flex items-center gap-2">
                    <i data-lucide="tag" class="w-4 h-4"></i>
                    Nama Produk
                </label>
                <input type="text" 
                       name="name" 
                       required 
                       placeholder="Contoh: Roti Bakar Coklat"
                       class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-400 outline-none">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-200 flex items-center gap-2">
                    <i data-lucide="dollar-sign" class="w-4 h-4"></i>
                    Harga (Rp)
                </label>
                <input type="number" 
                       name="price" 
                       required 
                       min="0" 
                       step="100"
                       placeholder="Contoh: 15000"
                       class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-400 outline-none">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-200 flex items-center gap-2">
                    <i data-lucide="package" class="w-4 h-4"></i>
                    Stok
                </label>
                <input type="number" 
                       name="stock" 
                       required 
                       min="0"
                       placeholder="Contoh: 50"
                       class="input-modern w-full px-4 py-3 rounded-xl text-white placeholder-gray-400 outline-none">
            </div>

            <div>
                <label class="block mb-2 font-semibold text-gray-200 flex items-center gap-2">
                    <i data-lucide="image" class="w-4 h-4"></i>
                    Gambar Produk (Optional)
                </label>
                <input type="file" 
                       name="image" 
                       id="imageInput"
                       accept="image/*"
                       onchange="previewImage(event)"
                       class="input-modern w-full px-4 py-3 rounded-xl text-white outline-none file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-500/20 file:text-blue-200 hover:file:bg-blue-500/30">
                <p class="text-xs text-gray-400 mt-2">Format: JPG, PNG, GIF, WEBP (Max 2MB)</p>
                
                <div id="imagePreview" class="mt-4 hidden">
                    <div class="glass-effect p-4 rounded-xl">
                        <p class="text-sm text-gray-300 mb-2">Preview:</p>
                        <img id="preview" src="" alt="Preview" class="w-48 h-48 object-cover rounded-xl border-2 border-white/20">
                    </div>
                </div>
            </div>

            <script>
            function previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview').src = e.target.result;
                        document.getElementById('imagePreview').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            }
            </script>

            <div class="flex gap-4 pt-4">
                <button type="submit" 
                        class="btn-primary flex-1 py-3 rounded-xl font-semibold flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i>
                    Simpan Produk
                </button>
                <a href="<?= BASE_URL ?>/product" 
                   class="glass-effect flex-1 py-3 rounded-xl font-semibold hover:bg-white/20 transition flex items-center justify-center gap-2">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
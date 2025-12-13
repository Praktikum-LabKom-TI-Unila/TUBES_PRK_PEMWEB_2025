<section class="p-6 md:p-10 space-y-6">
    <!-- HEADER -->
    <div class="flex flex-col gap-2">
        <h1 class="text-2xl font-semibold text-slate-900">Kategori Bahan</h1>
        <p class="text-sm text-slate-500">Kelola kategori untuk mengelompokkan bahan baku</p>
    </div>

    <!-- SEARCH & ADD -->
    <div class="flex flex-col sm:flex-row gap-4 sm:items-center sm:justify-between">
        <div class="relative flex-1">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                </svg>
            </span>
            <input 
                type="text" 
                id="searchInput"
                placeholder="Cari kategori..." 
                class="w-full rounded-2xl border border-slate-200 pl-12 pr-4 py-3 shadow-sm focus:ring-2 focus:ring-blue-100 focus:border-blue-400" 
            />
        </div>

        <button 
            id="btnAddCategory"
            onclick="if(typeof CategoryModule !== 'undefined') { CategoryModule.openModalForCreate(); } else { document.getElementById('categoryModal').classList.remove('hidden'); }"
            class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-green-500 hover:from-emerald-600 hover:to-green-600 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-sm transition-all"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
            </svg>
            Tambah Kategori
        </button>
    </div>

    <!-- LOADING STATE -->
    <div id="loadingState" class="hidden">
        <div class="flex items-center justify-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
        </div>
    </div>

    <!-- EMPTY STATE -->
    <div id="emptyState" class="hidden">
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>
            <h3 class="text-lg font-semibold text-slate-700 mb-2">Tidak Ada Kategori</h3>
            <p class="text-sm text-slate-500" id="emptyMessage">Belum ada kategori yang dibuat</p>
        </div>
    </div>

    <!-- GRID KATEGORI -->
    <div id="categoryGrid" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        <!-- Will be populated by JavaScript -->
    </div>

    <!-- PAGINATION -->
    <div id="paginationContainer" class="flex justify-center mt-6">
        <nav class="flex items-center gap-2" id="pagination">
            <!-- Will be populated by JavaScript -->
        </nav>
    </div>
</section>

<!-- MODAL CREATE/EDIT -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all" onclick="event.stopPropagation()">
        <!-- MODAL HEADER -->
        <div class="flex items-center justify-between p-6 border-b border-slate-100">
            <h3 id="modalTitle" class="text-xl font-semibold text-slate-900">Tambah Kategori</h3>
            <button 
                onclick="CategoryModule.closeModal()" 
                class="text-slate-400 hover:text-slate-600 transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- MODAL BODY -->
        <form id="categoryForm" class="p-6 space-y-5">
            <input type="hidden" id="categoryId" name="id">

            <!-- NAME -->
            <div>
                <label for="categoryName" class="block text-sm font-medium text-slate-700 mb-2">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="categoryName" 
                    name="name"
                    placeholder="Contoh: Tepung, Gula, Minyak"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all"
                    required
                />
                <p id="nameError" class="text-sm text-red-500 mt-1 hidden"></p>
            </div>

            <!-- DESCRIPTION -->
            <div>
                <label for="categoryDescription" class="block text-sm font-medium text-slate-700 mb-2">
                    Deskripsi
                </label>
                <textarea 
                    id="categoryDescription" 
                    name="description"
                    rows="3"
                    placeholder="Deskripsi kategori (opsional)"
                    class="w-full rounded-xl border border-slate-200 px-4 py-3 focus:ring-2 focus:ring-blue-100 focus:border-blue-400 transition-all resize-none"
                ></textarea>
                <p id="descriptionError" class="text-sm text-red-500 mt-1 hidden"></p>
            </div>

            <!-- BUTTONS -->
            <div class="flex gap-3 pt-2">
                <button 
                    type="button" 
                    onclick="CategoryModule.closeModal()"
                    class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition-colors"
                >
                    Batal
                </button>
                <button 
                    type="submit" 
                    id="btnSubmit"
                    class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-medium transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    <span id="submitText">Simpan</span>
                    <span id="submitSpinner" class="hidden">
                        <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL DELETE CONFIRMATION -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md transform transition-all" onclick="event.stopPropagation()">
        <!-- MODAL HEADER -->
        <div class="p-6 text-center">
            <div class="w-16 h-16 rounded-full bg-red-100 mx-auto mb-4 flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-slate-900 mb-2">Hapus Kategori?</h3>
            <p class="text-slate-600 mb-1">Anda yakin ingin menghapus kategori</p>
            <p class="text-slate-900 font-semibold mb-4" id="deleteItemName"></p>
            <p class="text-sm text-slate-500">Tindakan ini tidak dapat dibatalkan.</p>
        </div>

        <!-- BUTTONS -->
        <div class="flex gap-3 p-6 pt-0">
            <button 
                type="button" 
                onclick="CategoryModule.closeDeleteModal()"
                class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-slate-700 font-medium hover:bg-slate-50 transition-colors"
            >
                Batal
            </button>
            <button 
                type="button" 
                id="btnConfirmDelete"
                onclick="CategoryModule.confirmDelete()"
                class="flex-1 px-4 py-3 rounded-xl bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white font-medium transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <span id="deleteText">Ya, Hapus</span>
                <span id="deleteSpinner" class="hidden">
                    <svg class="animate-spin h-5 w-5 mx-auto" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
            </button>
        </div>
    </div>
</div>

<!-- TOAST NOTIFICATION -->
<div id="toast" class="hidden fixed top-4 right-4 z-50 max-w-sm w-full">
    <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 flex items-start gap-3">
        <div id="toastIcon" class="flex-shrink-0"></div>
        <div class="flex-1 min-w-0">
            <h4 id="toastTitle" class="font-semibold text-slate-900 text-sm"></h4>
            <p id="toastMessage" class="text-sm text-slate-600 mt-1"></p>
        </div>
        <button onclick="Toast.hide()" class="flex-shrink-0 text-slate-400 hover:text-slate-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
</div>

<!-- LOAD JAVASCRIPT MODULES -->
<script>
    // Log script URLs being loaded
    console.log('Loading scripts from:');
    console.log('- API:', '<?= asset('js/utils/api.js') ?>');
    console.log('- Toast:', '<?= asset('js/utils/toast.js') ?>');
    console.log('- Validator:', '<?= asset('js/utils/validator.js') ?>');
    console.log('- Category:', '<?= asset('js/modules/category.js') ?>');
</script>
<script src="<?= asset('js/utils/api.js') ?>"></script>
<script src="<?= asset('js/utils/toast.js') ?>"></script>
<script src="<?= asset('js/utils/validator.js') ?>"></script>
<script src="<?= asset('js/modules/category.js') ?>"></script>
<script>
    // Debug: Check if scripts loaded
    console.log('=== Script Loading Status ===');
    console.log('ApiClient:', typeof ApiClient);
    console.log('Toast:', typeof Toast);
    console.log('Validator:', typeof Validator);
    console.log('CategoryModule:', typeof CategoryModule);
    
    // Initialize module when DOM ready
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DOM Ready ===');
        if (typeof CategoryModule !== 'undefined') {
            try {
                CategoryModule.init();
                console.log('✅ CategoryModule initialized successfully');
            } catch(error) {
                console.error('❌ Error initializing CategoryModule:', error);
                alert('Error: ' + error.message);
            }
        } else {
            console.error('❌ CategoryModule is not defined!');
            console.log('Available objects:', Object.keys(window).filter(k => k.includes('Module') || k.includes('Api') || k.includes('Toast') || k.includes('Validator')));
        }
    });
</script>

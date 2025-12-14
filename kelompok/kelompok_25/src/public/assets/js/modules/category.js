/**
 * Category Module
 * Handles all category CRUD operations
 */
const CategoryModule = {
    // State
    state: {
        categories: [],
        currentPage: 1,
        perPage: 9,
        totalPages: 1,
        searchKeyword: '',
        editingId: null,
        deletingId: null
    },

    // Color gradients for cards
    gradients: [
        'from-blue-500 to-blue-400',
        'from-purple-500 to-fuchsia-500',
        'from-pink-500 to-rose-500',
        'from-orange-500 to-amber-500',
        'from-emerald-500 to-green-500',
        'from-red-400 to-rose-400',
        'from-cyan-500 to-blue-500',
        'from-violet-500 to-purple-500',
        'from-lime-500 to-green-400'
    ],

    // DOM Elements
    elements: {},

    /**
     * Initialize module
     */
    init() {
        console.log('CategoryModule.init() called');
        this.cacheElements();
        console.log('Elements cached:', this.elements.btnAddCategory ? 'Button found' : 'Button NOT found');
        this.bindEvents();
        console.log('Events bound');
        this.loadCategories();
    },

    /**
     * Cache DOM elements
     */
    cacheElements() {
        this.elements = {
            searchInput: document.getElementById('searchInput'),
            btnAddCategory: document.getElementById('btnAddCategory'),
            categoryGrid: document.getElementById('categoryGrid'),
            loadingState: document.getElementById('loadingState'),
            emptyState: document.getElementById('emptyState'),
            paginationContainer: document.getElementById('paginationContainer'),
            
            // Modal elements
            categoryModal: document.getElementById('categoryModal'),
            modalTitle: document.getElementById('modalTitle'),
            categoryForm: document.getElementById('categoryForm'),
            categoryId: document.getElementById('categoryId'),
            categoryName: document.getElementById('categoryName'),
            categoryDescription: document.getElementById('categoryDescription'),
            btnSubmit: document.getElementById('btnSubmit'),
            submitText: document.getElementById('submitText'),
            submitSpinner: document.getElementById('submitSpinner'),
            
            // Delete modal
            deleteModal: document.getElementById('deleteModal'),
            deleteItemName: document.getElementById('deleteItemName'),
            btnConfirmDelete: document.getElementById('btnConfirmDelete'),
            deleteText: document.getElementById('deleteText'),
            deleteSpinner: document.getElementById('deleteSpinner')
        };
    },

    /**
     * Bind event listeners
     */
    bindEvents() {
        // Search with debounce
        let searchTimeout;
        if (this.elements.searchInput) {
            this.elements.searchInput.addEventListener('input', (e) => {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    this.state.searchKeyword = e.target.value;
                    this.state.currentPage = 1;
                    this.loadCategories();
                }, 500);
            });
        }

        // Add button
        if (this.elements.btnAddCategory) {
            console.log('Binding click event to Add Category button');
            this.elements.btnAddCategory.addEventListener('click', (e) => {
                console.log('Add button clicked!');
                e.preventDefault();
                this.openModalForCreate();
            });
        } else {
            console.error('btnAddCategory element not found!');
        }

        // Form submit
        if (this.elements.categoryForm) {
            this.elements.categoryForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.handleSubmit();
            });
        }

        // Close modal on backdrop click
        if (this.elements.categoryModal) {
            this.elements.categoryModal.addEventListener('click', (e) => {
                if (e.target === this.elements.categoryModal) {
                    this.closeModal();
                }
            });
        }

        if (this.elements.deleteModal) {
            this.elements.deleteModal.addEventListener('click', (e) => {
                if (e.target === this.elements.deleteModal) {
                    this.closeDeleteModal();
                }
            });
        }
    },

    /**
     * Load categories from API
     */
    async loadCategories() {
        this.showLoading();

        try {
            const params = {
                page: this.state.currentPage,
                per_page: this.state.perPage
            };

            if (this.state.searchKeyword) {
                params.search = this.state.searchKeyword;
            }

            console.log('Fetching categories with params:', params);
            const response = await ApiClient.get('/categories', params);
            console.log('API Response:', response);

            if (response.success) {
                this.state.categories = response.data.data || [];
                this.state.currentPage = response.data.current_page || 1;
                this.state.totalPages = response.data.last_page || 1;
                
                console.log('Categories loaded:', this.state.categories.length, 'items');
                this.renderCategories();
                this.renderPagination();
            } else {
                throw new Error(response.message || 'Gagal memuat data');
            }
        } catch (error) {
            console.error('Load categories error:', error);
            
            let errorMsg = 'Gagal memuat data kategori';
            if (error.message) {
                errorMsg = error.message;
            }
            
            if (error.status === 0) {
                errorMsg = 'Tidak dapat terhubung ke server. Pastikan server berjalan.';
            } else if (error.status === 401) {
                errorMsg = 'Sesi Anda telah berakhir. Silakan login kembali.';
            } else if (error.status === 403) {
                errorMsg = 'Anda tidak memiliki akses untuk melihat data ini.';
            } else if (error.status >= 500) {
                errorMsg = 'Terjadi kesalahan pada server. Silakan coba lagi nanti.';
            }
            
            Toast.error(errorMsg);
            this.showEmpty(errorMsg);
        }
    },

    /**
     * Render categories grid
     */
    renderCategories() {
        if (this.state.categories.length === 0) {
            this.showEmpty(
                this.state.searchKeyword 
                    ? `Tidak ditemukan kategori dengan kata kunci "${this.state.searchKeyword}"`
                    : 'Belum ada kategori yang dibuat'
            );
            return;
        }

        const html = this.state.categories.map((category, index) => {
            const gradient = this.gradients[index % this.gradients.length];
            const materialCount = category.material_count || 0;

            return `
                <article class="rounded-3xl border border-slate-100 bg-white shadow-sm p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                    <!-- HEADER CARD -->
                    <div class="flex items-start gap-4">
                        <div class="h-12 w-12 rounded-2xl text-white flex items-center justify-center bg-gradient-to-br ${gradient}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7v6c0 .9.3 1.8.9 2.5l7.6 8.5c.6.7 1.7.8 2.4.2L21 19.4c.6-.6.9-1.5.9-2.4V7c0-1.7-1.3-3-3-3H6C4.3 4 3 5.3 3 7z" />
                            </svg>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg font-semibold text-slate-900 truncate">${this.escapeHtml(category.name)}</h2>
                            <p class="text-sm text-slate-500 mt-1 line-clamp-2">${category.description ? this.escapeHtml(category.description) : 'Tidak ada deskripsi'}</p>
                        </div>

                        <!-- ACTION -->
                        <div class="flex items-center gap-3 text-slate-400">
                            <button 
                                title="Edit" 
                                class="hover:text-blue-500 transition-colors"
                                onclick="CategoryModule.openModalForEdit(${category.id})"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                            </button>
                            <button 
                                title="Hapus" 
                                class="hover:text-rose-500 transition-colors"
                                onclick="CategoryModule.openDeleteModal(${category.id}, '${this.escapeHtml(category.name)}')"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- FOOTER -->
                    <div class="flex items-center gap-2 text-sm text-slate-600">
                        <span class="text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                        </span>
                        ${materialCount} Bahan
                    </div>
                </article>
            `;
        }).join('');

        this.elements.categoryGrid.innerHTML = html;
        this.elements.loadingState.classList.add('hidden');
        this.elements.emptyState.classList.add('hidden');
        this.elements.categoryGrid.classList.remove('hidden');
    },

    /**
     * Render pagination
     */
    renderPagination() {
        if (this.state.totalPages <= 1) {
            this.elements.paginationContainer.classList.add('hidden');
            return;
        }

        let html = '';

        // Previous button
        html += `
            <button 
                class="px-3 py-2 rounded-lg border ${this.state.currentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-200 text-slate-700 hover:bg-slate-50'}"
                ${this.state.currentPage === 1 ? 'disabled' : ''}
                onclick="CategoryModule.goToPage(${this.state.currentPage - 1})"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </button>
        `;

        // Page numbers
        for (let i = 1; i <= this.state.totalPages; i++) {
            if (
                i === 1 ||
                i === this.state.totalPages ||
                (i >= this.state.currentPage - 1 && i <= this.state.currentPage + 1)
            ) {
                html += `
                    <button 
                        class="px-4 py-2 rounded-lg font-medium ${i === this.state.currentPage ? 'bg-blue-500 text-white' : 'border border-slate-200 text-slate-700 hover:bg-slate-50'}"
                        onclick="CategoryModule.goToPage(${i})"
                    >
                        ${i}
                    </button>
                `;
            } else if (i === this.state.currentPage - 2 || i === this.state.currentPage + 2) {
                html += `<span class="px-2 text-slate-400">...</span>`;
            }
        }

        // Next button
        html += `
            <button 
                class="px-3 py-2 rounded-lg border ${this.state.currentPage === this.state.totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-200 text-slate-700 hover:bg-slate-50'}"
                ${this.state.currentPage === this.state.totalPages ? 'disabled' : ''}
                onclick="CategoryModule.goToPage(${this.state.currentPage + 1})"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            </button>
        `;

        document.getElementById('pagination').innerHTML = html;
        this.elements.paginationContainer.classList.remove('hidden');
    },

    /**
     * Go to specific page
     */
    goToPage(page) {
        if (page < 1 || page > this.state.totalPages || page === this.state.currentPage) {
            return;
        }
        this.state.currentPage = page;
        this.loadCategories();
    },

    /**
     * Show loading state
     */
    showLoading() {
        this.elements.loadingState.classList.remove('hidden');
        this.elements.emptyState.classList.add('hidden');
        this.elements.categoryGrid.classList.add('hidden');
        this.elements.paginationContainer.classList.add('hidden');
    },

    /**
     * Show empty state
     */
    showEmpty(message) {
        this.elements.emptyState.classList.remove('hidden');
        document.getElementById('emptyMessage').textContent = message;
        this.elements.loadingState.classList.add('hidden');
        this.elements.categoryGrid.classList.add('hidden');
        this.elements.paginationContainer.classList.add('hidden');
    },

    /**
     * Open modal for creating new category
     */
    openModalForCreate() {
        console.log('openModalForCreate() called');
        this.state.editingId = null;
        
        if (this.elements.modalTitle) {
            this.elements.modalTitle.textContent = 'Tambah Kategori';
        }
        if (this.elements.submitText) {
            this.elements.submitText.textContent = 'Simpan';
        }
        if (this.elements.categoryForm) {
            this.elements.categoryForm.reset();
        }
        if (this.elements.categoryId) {
            this.elements.categoryId.value = '';
        }
        if (typeof Validator !== 'undefined') {
            Validator.clearErrors();
        }
        if (this.elements.categoryModal) {
            console.log('Opening modal...');
            this.elements.categoryModal.classList.remove('hidden');
        } else {
            console.error('categoryModal element not found!');
        }
        if (this.elements.categoryName) {
            this.elements.categoryName.focus();
        }
    },

    /**
     * Open modal for editing category
     */
    async openModalForEdit(id) {
        const category = this.state.categories.find(c => c.id === id);
        if (!category) {
            Toast.error('Error', 'Kategori tidak ditemukan');
            return;
        }

        this.state.editingId = id;
        this.elements.modalTitle.textContent = 'Edit Kategori';
        this.elements.submitText.textContent = 'Update';
        this.elements.categoryId.value = category.id;
        this.elements.categoryName.value = category.name;
        this.elements.categoryDescription.value = category.description || '';
        Validator.clearErrors();
        this.elements.categoryModal.classList.remove('hidden');
        this.elements.categoryName.focus();
    },

    /**
     * Close modal
     */
    closeModal() {
        this.elements.categoryModal.classList.add('hidden');
        this.elements.categoryForm.reset();
        this.state.editingId = null;
        Validator.clearErrors();
    },

    /**
     * Handle form submit
     */
    async handleSubmit() {
        const formData = {
            name: this.elements.categoryName.value.trim(),
            description: this.elements.categoryDescription.value.trim()
        };

        // Client-side validation
        const validation = Validator.validateCategory(formData);
        if (!validation.valid) {
            Validator.displayErrors(validation.errors);
            return;
        }

        // Show loading
        this.elements.btnSubmit.disabled = true;
        this.elements.submitText.classList.add('hidden');
        this.elements.submitSpinner.classList.remove('hidden');

        try {
            let response;
            
            if (this.state.editingId) {
                // Update - use POST to /categories/{id} with formData
                response = await ApiClient.post(`/categories/${this.state.editingId}`, formData);
            } else {
                // Create
                response = await ApiClient.post('/categories', formData);
            }

            if (response.success) {
                Toast.success(
                    'Berhasil',
                    this.state.editingId ? 'Kategori berhasil diupdate' : 'Kategori berhasil ditambahkan'
                );
                this.closeModal();
                this.loadCategories();
            } else {
                throw new Error(response.message || 'Terjadi kesalahan');
            }
        } catch (error) {
            console.error('Submit error:', error);
            
            if (error.errors && Object.keys(error.errors).length > 0) {
                Validator.displayErrors(error.errors);
            } else {
                Toast.error('Gagal Menyimpan', error.message);
            }
        } finally {
            this.elements.btnSubmit.disabled = false;
            this.elements.submitText.classList.remove('hidden');
            this.elements.submitSpinner.classList.add('hidden');
        }
    },

    /**
     * Open delete confirmation modal
     */
    openDeleteModal(id, name) {
        this.state.deletingId = id;
        this.elements.deleteItemName.textContent = name;
        this.elements.deleteModal.classList.remove('hidden');
    },

    /**
     * Close delete modal
     */
    closeDeleteModal() {
        this.elements.deleteModal.classList.add('hidden');
        this.state.deletingId = null;
    },

    /**
     * Confirm delete
     */
    async confirmDelete() {
        if (!this.state.deletingId) return;

        // Show loading
        this.elements.btnConfirmDelete.disabled = true;
        this.elements.deleteText.classList.add('hidden');
        this.elements.deleteSpinner.classList.remove('hidden');

        try {
            // Use POST to /categories/{id}/delete endpoint
            const response = await ApiClient.post(`/categories/${this.state.deletingId}/delete`, {});

            if (response.success) {
                Toast.success('Berhasil', 'Kategori berhasil dihapus');
                this.closeDeleteModal();
                this.loadCategories();
            } else {
                throw new Error(response.message || 'Gagal menghapus kategori');
            }
        } catch (error) {
            console.error('Delete error:', error);
            Toast.error('Gagal Menghapus', error.message);
        } finally {
            this.elements.btnConfirmDelete.disabled = false;
            this.elements.deleteText.classList.remove('hidden');
            this.elements.deleteSpinner.classList.add('hidden');
        }
    },

    /**
     * Escape HTML to prevent XSS
     */
    escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.replace(/[&<>"']/g, m => map[m]);
    }
};

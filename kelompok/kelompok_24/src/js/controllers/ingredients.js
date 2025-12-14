/**
 * src/js/controllers/ingredients.js
 * Controller untuk CRUD Ingredients/Stock (ingredients.php API)
 */

(function (window, document) {
    'use strict';

    const STATUS_META = {
        'safe': { label: 'Aman', className: 'text-warkops-success' },
        'low': { label: 'Low Stock', className: 'text-warkops-accent' },
        'critical': { label: 'Critical', className: 'text-red-500 animate-pulse' }
    };

    let state = {
        stock: []
    };

    let nodes = {};

    function init() {
        collectNodes();
        bindEvents();
        loadData();
    }

    function collectNodes() {
        nodes = {
            tableBody: document.querySelector('#stock-table-body'),
            btnAdd: document.getElementById('btn-add-stock'),
            modal: document.getElementById('modal-stock'),
            form: document.getElementById('form-stock'),
            btnClose: document.getElementById('close-modal-stock'),
            btnCancel: document.getElementById('cancel-modal-stock'),
            title: document.getElementById('modal-stock-title'),
            statActive: document.getElementById('stat-active-stock')
        };
    }

    function bindEvents() {
        nodes.btnAdd?.addEventListener('click', () => openModal());
        nodes.btnClose?.addEventListener('click', closeModal);
        nodes.btnCancel?.addEventListener('click', closeModal);
        nodes.form?.addEventListener('submit', handleSubmit);
    }

    async function loadData() {
        await fetchStock();
        render();
        updateStats();
    }

    async function fetchStock() {
        try {
            const response = await fetch('api/ingredients.php');
            const result = await response.json();
            if (result.success) state.stock = result.data;
        } catch (error) {
            console.error("Failed to load stock:", error);
            nodes.tableBody.innerHTML = errorRow("Gagal memuat data stok.");
        }
    }

    function render() {
        const tbody = nodes.tableBody;
        if (!tbody) return;
        
        tbody.innerHTML = '';

        if (state.stock.length === 0) {
            tbody.innerHTML = emptyRow("Stok kosong.");
            return;
        }

        state.stock.forEach(item => {
            const qty = parseFloat(item.stock_qty);
            const threshold = parseFloat(item.low_stock_threshold || 5);
            
            let statusKey = 'safe';
            if (qty <= 0) statusKey = 'critical';
            else if (qty <= threshold) statusKey = 'low';

            const status = STATUS_META[statusKey];

            const row = `
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-3">
                        <div class="font-bold text-white">${item.name}</div>
                        <div class="text-[10px] text-warkops-muted font-mono">ID: ING-${String(item.ingredient_id).padStart(3, '0')}</div>
                    </td>
                    <td class="py-3 text-right font-mono text-white">
                        <span class="text-lg font-bold">${qty}</span> <span class="text-xs text-warkops-muted">${item.unit}</span>
                    </td>
                    <td class="py-3 text-right ${status.className} font-bold text-xs uppercase">${status.label}</td>
                    <td class="py-3 text-right">
                        <button onclick="IngredientsController.edit(${item.ingredient_id})" class="text-warkops-secondary hover:text-warkops-secondary/70 text-xs mr-2" title="Edit">
                            ✎
                        </button>
                        <button onclick="IngredientsController.delete(${item.ingredient_id})" class="text-red-500 hover:text-red-400 text-xs" title="Hapus">
                            ✕
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function updateStats() {
        if (nodes.statActive) {
            const activeCount = state.stock.filter(i => parseFloat(i.stock_qty) > 0).length;
            nodes.statActive.innerText = activeCount;
        }
    }

    function openModal(data = null) {
        if (!nodes.modal) return;
        
        nodes.modal.classList.remove('hidden');
        nodes.title.textContent = data ? 'Edit Bahan' : 'Tambah Bahan';
        
        if (data) {
            document.getElementById('stock-id').value = data.ingredient_id;
            document.getElementById('stock-name').value = data.name;
            document.getElementById('stock-qty').value = data.stock_qty;
            document.getElementById('stock-unit').value = data.unit;
            document.getElementById('stock-threshold').value = data.low_stock_threshold || '';
        } else {
            nodes.form.reset();
        }
    }

    function closeModal() {
        nodes.modal?.classList.add('hidden');
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        const stockId = document.getElementById('stock-id').value;
        const isEdit = !!stockId;
        
        if (isEdit) data.ingredient_id = parseInt(stockId);
        
        try {
            const url = isEdit ? `api/ingredients.php?id=${stockId}` : 'api/ingredients.php';
            const method = isEdit ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', result.message || (isEdit ? 'Bahan berhasil diupdate' : 'Bahan berhasil ditambahkan'));
                closeModal();
                await fetchStock();
                render();
                updateStats();
            } else {
                showToast('error', result.message || 'Gagal menyimpan bahan');
            }
        } catch (error) {
            console.error('Stock submit error:', error);
            showToast('error', 'Terjadi kesalahan saat menyimpan bahan');
        }
    }

    async function deleteItem(id) {
        if (!confirm('Yakin ingin menghapus bahan ini?')) return;
        
        try {
            const response = await fetch(`api/ingredients.php?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Bahan berhasil dihapus');
                await fetchStock();
                render();
                updateStats();
            } else {
                showToast('error', result.message || 'Gagal menghapus bahan');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus bahan');
        }
    }

    function edit(id) {
        const item = state.stock.find(s => s.ingredient_id == id);
        if (item) openModal(item);
    }

    // Utils
    function emptyRow(msg) {
        return `<tr><td colspan="4" class="py-8 text-center text-warkops-muted font-mono text-xs border-t border-white/5">${msg}</td></tr>`;
    }

    function errorRow(msg) {
        return `<tr><td colspan="4" class="py-8 text-center text-red-500 font-mono text-xs border-t border-red-500/20 bg-red-500/5">${msg}</td></tr>`;
    }

    function showToast(type, message) {
        if (window.ToastNotification?.show) {
            window.ToastNotification.show(type, message);
        } else {
            alert(message);
        }
    }

    // Public API
    window.IngredientsController = {
        init,
        edit,
        delete: deleteItem
    };

})(window, document);

/**
 * src/js/controllers/recipes.js
 * Controller untuk CRUD Recipes (recipes.php API)
 */

(function (window, document) {
    'use strict';

    let state = {
        recipes: [],
        menu: [],
        ingredients: []
    };

    let nodes = {};

    function init() {
        collectNodes();
        bindEvents();
        loadData();
    }

    function collectNodes() {
        nodes = {
            tableBody: document.querySelector('#recipe-table-body'),
            btnAdd: document.getElementById('btn-add-recipe'),
            modal: document.getElementById('modal-recipe'),
            form: document.getElementById('form-recipe'),
            btnClose: document.getElementById('close-modal-recipe'),
            btnCancel: document.getElementById('cancel-modal-recipe'),
            title: document.getElementById('modal-recipe-title')
        };
    }

    function bindEvents() {
        nodes.btnAdd?.addEventListener('click', () => openModal());
        nodes.btnClose?.addEventListener('click', closeModal);
        nodes.btnCancel?.addEventListener('click', closeModal);
        nodes.form?.addEventListener('submit', handleSubmit);
    }

    async function loadData() {
        await Promise.all([
            fetchRecipes(),
            fetchMenu(),
            fetchIngredients()
        ]);
        render();
    }

    async function fetchRecipes() {
        try {
            const response = await fetch('api/recipes.php');
            const result = await response.json();
            if (result.success) state.recipes = result.data;
        } catch (error) {
            console.error("Failed to load recipes:", error);
            nodes.tableBody.innerHTML = errorRow("Gagal memuat data resep.");
        }
    }

    async function fetchMenu() {
        try {
            const response = await fetch('api/items.php');
            const result = await response.json();
            if (result.success) state.menu = result.data;
        } catch (error) {
            console.error("Failed to load menu for recipes:", error);
        }
    }

    async function fetchIngredients() {
        try {
            const response = await fetch('api/ingredients.php');
            const result = await response.json();
            if (result.success) state.ingredients = result.data;
        } catch (error) {
            console.error("Failed to load ingredients for recipes:", error);
        }
    }

    function render() {
        const tbody = nodes.tableBody;
        if (!tbody) return;
        
        tbody.innerHTML = '';

        if (state.recipes.length === 0) {
            tbody.innerHTML = emptyRow("Belum ada resep.");
            return;
        }

        state.recipes.forEach(recipe => {
            const row = `
                <tr class="hover:bg-white/5 transition-colors">
                    <td class="py-3">
                        <div class="font-bold text-white">${recipe.menu_name || 'Unknown Menu'}</div>
                        <div class="text-[10px] text-warkops-muted font-mono">ID: RCP-${String(recipe.recipe_id).padStart(3, '0')}</div>
                    </td>
                    <td class="py-3">
                        <div class="text-white/80">${recipe.ingredient_name || 'Unknown Ingredient'}</div>
                        <div class="text-[10px] text-warkops-muted">${recipe.unit || '-'}</div>
                    </td>
                    <td class="py-3 text-right font-mono text-warkops-accent font-bold">${recipe.qty_used}</td>
                    <td class="py-3 text-right">
                        <button onclick="RecipesController.edit(${recipe.recipe_id})" class="text-warkops-secondary hover:text-warkops-secondary/70 text-xs mr-2" title="Edit">
                            ✎
                        </button>
                        <button onclick="RecipesController.delete(${recipe.recipe_id})" class="text-red-500 hover:text-red-400 text-xs" title="Hapus">
                            ✕
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function openModal(data = null) {
        if (!nodes.modal) return;
        
        nodes.modal.classList.remove('hidden');
        nodes.title.textContent = data ? 'Edit Resep' : 'Tambah Resep';
        populateDropdowns();
        
        if (data) {
            document.getElementById('recipe-id').value = data.recipe_id;
            document.getElementById('recipe-menu').value = data.menu_id;
            document.getElementById('recipe-ingredient').value = data.ingredient_id;
            document.getElementById('recipe-qty').value = data.qty_used;
            document.getElementById('recipe-unit').value = data.unit || '';
        } else {
            nodes.form.reset();
        }
    }

    function closeModal() {
        nodes.modal?.classList.add('hidden');
    }

    function populateDropdowns() {
        const menuSelect = document.getElementById('recipe-menu');
        const ingredientSelect = document.getElementById('recipe-ingredient');
        
        if (menuSelect) {
            menuSelect.innerHTML = '<option value="">-- Pilih Menu --</option>';
            state.menu.forEach(item => {
                menuSelect.innerHTML += `<option value="${item.menu_id}">${item.name}</option>`;
            });
        }
        
        if (ingredientSelect) {
            ingredientSelect.innerHTML = '<option value="">-- Pilih Bahan --</option>';
            state.ingredients.forEach(item => {
                ingredientSelect.innerHTML += `<option value="${item.ingredient_id}">${item.name}</option>`;
            });
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData);
        
        const recipeId = document.getElementById('recipe-id').value;
        const isEdit = !!recipeId;
        
        if (isEdit) data.recipe_id = parseInt(recipeId);
        
        try {
            const url = isEdit ? `api/recipes.php?id=${recipeId}` : 'api/recipes.php';
            const method = isEdit ? 'PUT' : 'POST';
            
            const response = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            
            if (result.success) {
                showToast('success', result.message || (isEdit ? 'Resep berhasil diupdate' : 'Resep berhasil ditambahkan'));
                closeModal();
                await fetchRecipes();
                render();
            } else {
                showToast('error', result.message || 'Gagal menyimpan resep');
            }
        } catch (error) {
            console.error('Recipe submit error:', error);
            showToast('error', 'Terjadi kesalahan saat menyimpan resep');
        }
    }

    async function deleteItem(id) {
        if (!confirm('Yakin ingin menghapus resep ini?')) return;
        
        try {
            const response = await fetch(`api/recipes.php?id=${id}`, { method: 'DELETE' });
            const result = await response.json();
            
            if (result.success) {
                showToast('success', 'Resep berhasil dihapus');
                await fetchRecipes();
                render();
            } else {
                showToast('error', result.message || 'Gagal menghapus resep');
            }
        } catch (error) {
            console.error('Delete error:', error);
            showToast('error', 'Terjadi kesalahan saat menghapus resep');
        }
    }

    function edit(id) {
        const item = state.recipes.find(r => r.recipe_id == id);
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
    window.RecipesController = {
        init,
        edit,
        delete: deleteItem
    };

})(window, document);

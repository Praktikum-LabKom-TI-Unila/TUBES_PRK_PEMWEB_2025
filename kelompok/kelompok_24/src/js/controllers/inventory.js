/**
 * src/js/controllers/inventory.js
 * Orchestrator untuk Inventory Module
 * Koordinasi antara MenuController, IngredientsController, dan RecipesController
 */

(function (window, document) {
    'use strict';

    /**
     * Inisialisasi semua sub-controller
     */
    function init() {
        // Load scripts untuk setiap controller jika belum ada
        loadControllerScripts().then(() => {
            // Initialize sub-controllers
            if (window.MenuController) {
                MenuController.init();
            }
            
            if (window.IngredientsController) {
                IngredientsController.init();
            }
            
            if (window.RecipesController) {
                RecipesController.init();
            }
        });
    }

    /**
     * Load controller scripts dynamically
     */
    async function loadControllerScripts() {
        const scripts = [
            'js/controllers/menu.js',
            'js/controllers/ingredients.js',
            'js/controllers/recipes.js'
        ];

        const promises = scripts.map(src => {
            // Cek apakah script sudah di-load
            if (document.querySelector(`script[src="${src}"]`)) {
                return Promise.resolve();
            }

            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = src;
                script.onload = resolve;
                script.onerror = reject;
                document.body.appendChild(script);
            });
        });

        try {
            await Promise.all(promises);
        } catch (error) {
            console.error('Failed to load inventory sub-controllers:', error);
        }
    }

    // Expose Global untuk Router
    window.InventoryController = {
        init
    };

})(window, document);
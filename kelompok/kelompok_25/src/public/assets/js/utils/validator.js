/**
 * Form Validation Utility
 * Client-side validation helpers
 */
const Validator = {
    /**
     * Validate required field
     */
    required(value, fieldName = 'Field') {
        if (!value || value.trim() === '') {
            return `${fieldName} wajib diisi`;
        }
        return null;
    },

    /**
     * Validate minimum length
     */
    minLength(value, min, fieldName = 'Field') {
        if (value && value.length < min) {
            return `${fieldName} minimal ${min} karakter`;
        }
        return null;
    },

    /**
     * Validate maximum length
     */
    maxLength(value, max, fieldName = 'Field') {
        if (value && value.length > max) {
            return `${fieldName} maksimal ${max} karakter`;
        }
        return null;
    },

    /**
     * Validate email format
     */
    email(value, fieldName = 'Email') {
        if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            return `${fieldName} tidak valid`;
        }
        return null;
    },

    /**
     * Validate numeric value
     */
    numeric(value, fieldName = 'Field') {
        if (value && isNaN(value)) {
            return `${fieldName} harus berupa angka`;
        }
        return null;
    },

    /**
     * Validate minimum value
     */
    min(value, min, fieldName = 'Field') {
        if (value && parseFloat(value) < min) {
            return `${fieldName} minimal ${min}`;
        }
        return null;
    },

    /**
     * Validate maximum value
     */
    max(value, max, fieldName = 'Field') {
        if (value && parseFloat(value) > max) {
            return `${fieldName} maksimal ${max}`;
        }
        return null;
    },

    /**
     * Validate phone number (Indonesian format)
     */
    phone(value, fieldName = 'Nomor telepon') {
        if (value && !/^(\+62|62|0)[0-9]{9,12}$/.test(value.replace(/[\s-]/g, ''))) {
            return `${fieldName} tidak valid`;
        }
        return null;
    },

    /**
     * Validate URL format
     */
    url(value, fieldName = 'URL') {
        if (value) {
            try {
                new URL(value);
            } catch (e) {
                return `${fieldName} tidak valid`;
            }
        }
        return null;
    },

    /**
     * Validate category form
     */
    validateCategory(data) {
        const errors = {};

        // Name validation
        const nameError = this.required(data.name, 'Nama kategori') ||
                         this.minLength(data.name, 2, 'Nama kategori') ||
                         this.maxLength(data.name, 100, 'Nama kategori');
        if (nameError) errors.name = nameError;

        // Description validation (optional)
        if (data.description) {
            const descError = this.maxLength(data.description, 255, 'Deskripsi');
            if (descError) errors.description = descError;
        }

        return {
            valid: Object.keys(errors).length === 0,
            errors
        };
    },

    /**
     * Validate supplier form
     */
    validateSupplier(data) {
        const errors = {};

        // Name validation
        const nameError = this.required(data.name, 'Nama supplier') ||
                         this.minLength(data.name, 2, 'Nama supplier') ||
                         this.maxLength(data.name, 100, 'Nama supplier');
        if (nameError) errors.name = nameError;

        // Email validation (optional)
        if (data.email) {
            const emailError = this.email(data.email);
            if (emailError) errors.email = emailError;
        }

        // Phone validation (optional)
        if (data.phone) {
            const phoneError = this.phone(data.phone);
            if (phoneError) errors.phone = phoneError;
        }

        // Address validation (optional)
        if (data.address) {
            const addressError = this.maxLength(data.address, 255, 'Alamat');
            if (addressError) errors.address = addressError;
        }

        return {
            valid: Object.keys(errors).length === 0,
            errors
        };
    },

    /**
     * Validate material form
     */
    validateMaterial(data) {
        const errors = {};

        // Name validation
        const nameError = this.required(data.name, 'Nama bahan') ||
                         this.minLength(data.name, 2, 'Nama bahan') ||
                         this.maxLength(data.name, 100, 'Nama bahan');
        if (nameError) errors.name = nameError;

        // Category validation
        const categoryError = this.required(data.category_id, 'Kategori');
        if (categoryError) errors.category_id = categoryError;

        // Supplier validation
        const supplierError = this.required(data.supplier_id, 'Supplier');
        if (supplierError) errors.supplier_id = supplierError;

        // Unit validation
        const unitError = this.required(data.unit, 'Satuan');
        if (unitError) errors.unit = unitError;

        // Minimum stock validation
        const minStockError = this.required(data.minimum_stock, 'Stok minimum') ||
                             this.numeric(data.minimum_stock, 'Stok minimum') ||
                             this.min(data.minimum_stock, 0, 'Stok minimum');
        if (minStockError) errors.minimum_stock = minStockError;

        return {
            valid: Object.keys(errors).length === 0,
            errors
        };
    },

    /**
     * Display validation errors in form
     */
    displayErrors(errors) {
        // Clear previous errors
        document.querySelectorAll('[id$="Error"]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });

        // Remove error styling from inputs
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-red-300', 'focus:ring-red-100', 'focus:border-red-400');
        });

        // Display new errors
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(`${field}Error`);
            const inputElement = document.querySelector(`[name="${field}"]`);

            if (errorElement) {
                errorElement.textContent = errors[field];
                errorElement.classList.remove('hidden');
            }

            if (inputElement) {
                inputElement.classList.add('border-red-300', 'focus:ring-red-100', 'focus:border-red-400');
            }
        });
    },

    /**
     * Clear all validation errors
     */
    clearErrors() {
        document.querySelectorAll('[id$="Error"]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });

        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-red-300', 'focus:ring-red-100', 'focus:border-red-400');
        });
    }
};

/**
 * Reports Module
 * Handles stock and transaction report functionality
 */

const Reports = (() => {
    // Stock Report Functions
    const stockReport = {
        // Search and filter debounce timer
        debounceTimer: null,

        init() {
            this.attachEventListeners();
            this.loadInitialData();
        },

        attachEventListeners() {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const exportBtn = document.querySelector('[onclick="exportExcel()"]');

            if (searchInput) {
                searchInput.addEventListener('input', () => this.handleSearch());
            }
            if (categoryFilter) {
                categoryFilter.addEventListener('change', () => this.applyFilters());
            }
            if (statusFilter) {
                statusFilter.addEventListener('change', () => this.applyFilters());
            }
        },

        handleSearch() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.applyFilters();
            }, 500);
        },

        applyFilters() {
            const search = document.getElementById('searchInput')?.value || '';
            const category = document.getElementById('categoryFilter')?.value || '';
            const status = document.getElementById('statusFilter')?.value || '';

            const params = new URLSearchParams();
            if (search) params.append('search', search);
            if (category) params.append('category', category);
            if (status) params.append('status', status);

            window.location.href = '/reports/stock?' + params.toString();
        },

        loadInitialData() {
            // Data sudah diload dari server, tidak perlu AJAX
            console.log('Stock report loaded');
        }
    };

    // Transaction Report Functions
    const transactionReport = {
        chartInstance: null,

        init() {
            this.attachEventListeners();
            this.initializeChart();
        },

        attachEventListeners() {
            const typeFilter = document.getElementById('typeFilter');
            const filterBtn = document.querySelector('[onclick="applyFilter()"]');

            if (typeFilter) {
                typeFilter.addEventListener('change', () => this.updateChart());
            }
        },

        applyFilter() {
            const type = document.getElementById('typeFilter')?.value || 'all';
            const startDate = document.getElementById('startDate')?.value || '';
            const endDate = document.getElementById('endDate')?.value || '';

            const params = new URLSearchParams();
            if (type !== 'all') params.append('type', type);
            if (startDate) params.append('start_date', startDate);
            if (endDate) params.append('end_date', endDate);

            window.location.href = '/reports/transactions?' + params.toString();
        },

        initializeChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx || typeof Chart === 'undefined') {
                console.warn('Chart element or Chart.js not found');
                return;
            }

            this.destroyChart();
            this.createChart();
        },

        destroyChart() {
            if (this.chartInstance) {
                this.chartInstance.destroy();
                this.chartInstance = null;
            }
        },

        createChart() {
            const dates = [];
            const stockInValues = [];
            const stockOutValues = [];

            // Generate dummy data for last 7 days
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                dates.push(date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
                stockInValues.push(Math.random() * 5000000);
                stockOutValues.push(Math.random() * 3000000);
            }

            const ctx = document.getElementById('trendChart');
            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Stok Masuk (Rp)',
                            data: stockInValues,
                            borderColor: '#10B981',
                            backgroundColor: 'rgba(16, 185, 129, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#10B981'
                        },
                        {
                            label: 'Stok Keluar (Est.)',
                            data: stockOutValues,
                            borderColor: '#F97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            tension: 0.4,
                            fill: true,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#F97316'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 12 }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => {
                                    if (value >= 1000000) {
                                        return (value / 1000000).toFixed(1) + 'M';
                                    }
                                    return 'Rp ' + value.toLocaleString('id-ID');
                                }
                            }
                        }
                    }
                }
            });
        },

        updateChart() {
            this.initializeChart();
        }
    };

    // Export Functions
    const exportFunctions = {
        exportExcel() {
            alert('Fitur export Excel akan segera tersedia!\nTutorial: Download data stok dalam format Excel');
        },

        exportCSV() {
            alert('Fitur export CSV akan segera tersedia!\nTutorial: Download data transaksi dalam format CSV');
        }
    };

    // Public API
    return {
        initStockReport() {
            stockReport.init();
        },

        initTransactionReport() {
            transactionReport.init();
        },

        exportExcel() {
            exportFunctions.exportExcel();
        },

        exportCSV() {
            exportFunctions.exportCSV();
        }
    };
})();

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    // Check which report page we're on
    if (document.querySelector('h1')?.textContent.includes('Stok')) {
        Reports.initStockReport();
    } else if (document.querySelector('h1')?.textContent.includes('Transaksi')) {
        Reports.initTransactionReport();
    }
});

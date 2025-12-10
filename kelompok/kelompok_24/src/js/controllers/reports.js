/**
 * src/js/controllers/reports.js
 * Controller untuk Analytics & Visualization
 * Adapted for WarkOps v2.0 (New UI Structure)
 */

window.ReportsController = {
    state: {
        range: '7d',
        operator: 'all',
        chartData: [],
        historyData: []
    },

    init() {
        console.log("Reports Controller Initialized");
        this.cacheDom();
        this.bindEvents();
        
        // Load default view
        this.fetchData(); 
        
        // Auto refresh setiap 60 detik
        this.interval = setInterval(() => this.fetchData(), 60000);
    },

    destroy() {
        if (this.interval) clearInterval(this.interval);
    },

    cacheDom() {
        this.dom = {
            // Filters
            rangeSelect: document.getElementById('filter-range'),
            dateStart: document.getElementById('filter-start-date'),
            dateEnd: document.getElementById('filter-end-date'),
            operatorSelect: document.getElementById('filter-operator'),
            applyBtn: document.getElementById('filter-apply'),
            
            // Metrics
            valRevenue: document.getElementById('val-revenue'),
            valTrx: document.getElementById('val-transactions'),
            valBestItem: document.getElementById('val-best-seller'),
            valBestUnit: document.getElementById('val-best-unit'),
            
            // Chart & Table
            chartBody: document.getElementById('chart-body'),
            historyBody: document.getElementById('history-table-body')
        };
    },

    bindEvents() {
        const d = this.dom;
        if (!d.applyBtn) return;

        // Toggle Date Input Visibility
        d.rangeSelect.addEventListener('change', (e) => {
            const isCustom = e.target.value === 'custom';
            // Simple visual toggle logic could go here if needed
            if (isCustom) {
                d.dateStart.focus();
            }
        });

        // Apply Filter
        d.applyBtn.addEventListener('click', () => {
            this.state.range = d.rangeSelect.value;
            this.state.operator = d.operatorSelect.value;
            this.fetchData();
        });
    },

    async fetchData() {
        try {
            // Construct Query Params
            const params = new URLSearchParams({
                range: this.state.range,
                operator_id: this.state.operator
            });

            // Handle Custom Date
            if (this.state.range === 'custom') {
                params.append('start_date', this.dom.dateStart.value);
                params.append('end_date', this.dom.dateEnd.value);
            }

            // Fetch API
            const res = await fetch(`api/reports.php?${params.toString()}`);
            const json = await res.json();

            if (json.success) {
                // Populate Operator Dropdown (First Load Only)
                if (this.dom.operatorSelect.options.length <= 1 && json.filters?.operators) {
                    this.renderOperators(json.filters.operators);
                }

                this.renderMetrics(json.metrics);
                this.renderChart(json.daily_sales);
                this.renderHistory(json.transaction_history);
            } else {
                console.error("API Error:", json.message);
            }

        } catch (e) {
            console.error("Reports Fetch Error:", e);
        }
    },

    renderOperators(operators) {
        operators.forEach(op => {
            const opt = document.createElement('option');
            opt.value = op.id;
            opt.innerText = op.label;
            this.dom.operatorSelect.appendChild(opt);
        });
    },

    renderMetrics(metrics) {
        if (!metrics) return;
        
        const fmt = new Intl.NumberFormat('id-ID', { notation: "compact", maximumFractionDigits: 1 });
        
        // Update Text
        this.dom.valRevenue.innerText = 'Rp ' + fmt.format(metrics.total_revenue || 0);
        this.dom.valTrx.innerText = (metrics.total_transactions || 0).toLocaleString();
        
        // Best Seller Logic
        const bestName = metrics.best_seller_name || '-';
        const bestUnit = metrics.best_seller_units || 0;
        
        this.dom.valBestItem.innerText = bestName;
        this.dom.valBestUnit.innerText = `${bestUnit} sold in period`;
    },

    /**
     * RENDER CHART (Bar Chart Style)
     * Menggambar diagram batang di dalam #chart-body
     */
    renderChart(dailyData) {
        const container = this.dom.chartBody;
        if (!container) return;

        container.innerHTML = ''; // Clear

        // Convert Object to Array & Sort by Date
        const dataArr = Object.entries(dailyData || {}).map(([date, val]) => ({ date, val }));
        
        if (dataArr.length === 0) {
            container.innerHTML = '<div class="w-full text-center text-xs text-warkops-muted self-center">NO DATA</div>';
            return;
        }

        // Find Max Value for Scaling
        const maxVal = Math.max(...dataArr.map(d => d.val), 1000); // Min scale 1000 perak

        dataArr.forEach(d => {
            const heightPct = (d.val / maxVal) * 100; // 0-100%
            const dayLabel = new Date(d.date).toLocaleDateString('id-ID', { weekday: 'short' });
            const valFormatted = parseInt(d.val).toLocaleString();

            const barHtml = `
                <div class="flex-1 flex flex-col items-center justify-end h-full group relative cursor-crosshair">
                    <!-- Tooltip -->
                    <div class="absolute bottom-full mb-2 opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                        <div class="bg-black border border-white/20 px-2 py-1 text-[10px] text-white whitespace-nowrap shadow-lg">
                            <span class="text-warkops-secondary font-bold">Rp ${valFormatted}</span>
                            <br><span class="text-gray-500">${d.date}</span>
                        </div>
                    </div>

                    <!-- The Bar -->
                    <div class="w-full mx-1 bg-warkops-primary/20 border-t border-warkops-primary group-hover:bg-warkops-primary/60 transition-all relative" 
                         style="height: ${Math.max(heightPct, 2)}%">
                         <!-- Glow Effect on Peak -->
                         ${heightPct === 100 ? '<div class="absolute inset-0 bg-warkops-primary/20 animate-pulse"></div>' : ''}
                    </div>

                    <!-- Label -->
                    <div class="mt-2 text-[9px] font-mono text-warkops-muted uppercase group-hover:text-white transition-colors">
                        ${dayLabel}
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', barHtml);
        });
    },

    renderHistory(history) {
        const tbody = this.dom.historyBody;
        if (!tbody) return;

        tbody.innerHTML = '';

        if (!history || history.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="py-8 text-center text-xs text-warkops-muted">No transactions found in this period.</td></tr>`;
            return;
        }

        history.forEach(tx => {
            const statusClass = tx.status === 'PAID' ? 'text-warkops-success' : 'text-warkops-accent';
            
            const row = `
                <tr class="hover:bg-white/5 transition-colors border-b border-white/5 last:border-0 group">
                    <td class="py-3 px-4 font-mono text-xs text-warkops-secondary group-hover:underline">#${tx.trx_id}</td>
                    <td class="py-3 px-4 text-xs text-white/70">${tx.datetime}</td>
                    <td class="py-3 px-4 text-xs font-bold uppercase text-white">${tx.operator_username || 'SYSTEM'}</td>
                    <td class="py-3 px-4 text-xs text-warkops-muted truncate max-w-[200px]" title="${tx.item_summary}">${tx.item_summary || '-'}</td>
                    <td class="py-3 px-4 text-right font-mono text-xs font-bold text-white">Rp ${parseInt(tx.total).toLocaleString()}</td>
                    <td class="py-3 px-4 text-center text-[10px] font-bold ${statusClass}">${tx.status}</td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', row);
        });
    }
};
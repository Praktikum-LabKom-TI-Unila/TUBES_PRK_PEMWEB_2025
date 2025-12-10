(function() {
    const API_URL = 'http://localhost/TUBES_PRK_PEMWEB_2025/kelompok/kelompok_24/src/api/home.php';

    const dom = {
        sales: document.getElementById('d-total-sales'),
        growth: document.getElementById('d-sales-growth'),
        orders: document.getElementById('d-total-orders'),
        chart: document.getElementById('d-chart-container'),
        popular: document.getElementById('d-popular-list')
    };

    const formatCompact = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            notation: "compact",
            compactDisplay: "short",
            maximumFractionDigits: 1
        }).format(number);
    };

    const formatFull = (number) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    };

    async function initDashboard() {
        try {
            const response = await fetch(API_URL);
            const result = await response.json();

            if (result.success) {
                updateStatistics(result.data);
                generateChart(result.data.weekly_chart);
                generatePopularList(result.data.popular_items);
            } else {
                console.error(result.message);
            }

        } catch (error) {
            console.error(error);
        }
    }

    function updateStatistics(data) {
        if (dom.sales) {
            dom.sales.textContent = formatCompact(data.sales_today);
            dom.sales.classList.remove('animate-pulse');
        }

        if (dom.orders) {
            dom.orders.textContent = data.orders_today;
            dom.orders.classList.remove('animate-pulse');
        }

        if (dom.growth) {
            const percent = parseFloat(data.growth_percent);
            const isPositive = percent >= 0;
            const symbol = isPositive ? '▲' : '▼';
            const colorClass = isPositive ? 'text-warkops-success' : 'text-red-500';
            
            dom.growth.className = `font-bold ${colorClass}`;
            dom.growth.textContent = `${symbol} ${Math.abs(percent)}%`;
        }
    }

    function generateChart(chartData) {
        if (!dom.chart) return;
        dom.chart.innerHTML = '';

        if (!chartData || chartData.length === 0) {
            dom.chart.innerHTML = '<div class="text-xs text-white self-center">No Data</div>';
            return;
        }

        const maxVal = Math.max(...chartData.map(d => parseFloat(d.daily_total))) || 1;

        chartData.forEach(day => {
            const val = parseFloat(day.daily_total);
            const heightPercent = (val / maxVal) * 100;
            
            const bar = document.createElement('div');
            
            bar.className = `
                w-full 
                bg-warkops-primary/40 
                hover:bg-warkops-primary 
                border-t border-white/20 
                relative group 
                cursor-pointer 
                transition-all duration-500 ease-out
            `;
            
            bar.style.height = `${Math.max(heightPercent, 5)}%`;

            const dateLabel = new Date(day.date).toLocaleDateString('id-ID', { weekday: 'short' });
            bar.innerHTML = `
                <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 
                            opacity-0 group-hover:opacity-100 transition-opacity z-20 pointer-events-none">
                    <div class="bg-black border border-white/20 text-white text-[10px] px-2 py-1 font-mono whitespace-nowrap shadow-lg">
                        <div class="font-bold text-warkops-primary">${dateLabel}</div>
                        <div>${formatFull(val)}</div>
                    </div>
                </div>
            `;

            dom.chart.appendChild(bar);
        });
    }

    function generatePopularList(items) {
        if (!dom.popular) return;
        dom.popular.innerHTML = '';

        if (!items || items.length === 0) {
            dom.popular.innerHTML = '<div class="text-xs text-warkops-muted italic text-center py-4">Belum ada transaksi hari ini.</div>';
            return;
        }

        items.forEach((item, index) => {
            const rankIcon = index === 0 ? '👑' : '⭐';
            const rankColor = index === 0 ? 'text-yellow-400' : 'text-warkops-muted';
            const borderClass = index === items.length - 1 ? '' : 'border-b border-white/5';

            const itemHTML = `
                <div class="flex items-center gap-3 pb-3 ${borderClass} group cursor-default">
                    <div class="w-8 h-8 bg-white/5 flex items-center justify-center text-sm border border-white/10 rounded-sm ${rankColor}">
                        ${rankIcon}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm font-bold text-white truncate group-hover:text-warkops-primary transition-colors">
                            ${item.name}
                        </div>
                        <div class="text-[10px] text-warkops-muted font-mono">
                            <span class="text-white font-bold">${item.sold_qty}</span> terjual hari ini
                        </div>
                    </div>
                </div>
            `;
            
            dom.popular.insertAdjacentHTML('beforeend', itemHTML);
        });
    }

    initDashboard();
})();
class LiveDashboard {
    constructor() {
        this.metrics = {
            processing: 0,
            totalUmkm: 0,
            lastUpdate: Date.now()
        };
        
        this.config = {
            updateInterval: 5000 // 5 detik
        };
        
        this.elements = {
            counter: document.querySelector('#live-counter'),
            umkmCounter: document.querySelector('#umkm-counter')
        };
        
        // Cek preferensi reduced motion
        this.prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        this.init();
    }
    
    init() {
        // Validasi elemen ada
        if (!this.elements.counter || !this.elements.umkmCounter) {
            console.warn('LiveDashboard: Elemen yang diperlukan tidak ditemukan');
            return;
        }
        
        // Muat data awal
        this.loadAllStats();
        
        // Mulai update real-time
        this.startRealTimeUpdates();
        
        // Jeda update ketika halaman tersembunyi (optimasi performa)
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.pauseUpdates();
            } else {
                this.resumeUpdates();
            }
        });
    }
    
    async loadAllStats() {
        await Promise.all([
            this.loadPengaduanStats(),
            this.loadUmkmStats()
        ]);
    }
    
    async loadPengaduanStats() {
        try {
            const response = await fetch('api/get_pengaduan_stats.php');
            const data = await response.json();
            if (data.success) {
                const oldProcessing = this.metrics.processing;
                this.metrics.processing = data.processing;
                
                // Selalu render saat pertama kali load, atau ketika nilai berubah
                if (oldProcessing !== this.metrics.processing || oldProcessing === 0) {
                    this.renderProcessingCounter();
                }
            }
        } catch (error) {
            console.error('Gagal memuat statistik pengaduan:', error);
            this.elements.counter.textContent = '0';
        }
    }
    
    async loadUmkmStats() {
        try {
            const response = await fetch('api/get_umkm_stats.php');
            const data = await response.json();
            if (data.success) {
                const oldUmkm = this.metrics.totalUmkm;
                this.metrics.totalUmkm = data.total_umkm;
                
                // Selalu render saat pertama kali load, atau ketika nilai berubah
                this.renderUmkmCounter();
            }
        } catch (error) {
            console.error('Gagal memuat statistik UMKM:', error);
            this.elements.umkmCounter.textContent = '0';
        }
    }
    
    startRealTimeUpdates() {
        // Muat statistik segera
        this.loadAllStats();
        
        // Kemudian refresh setiap 5 detik
        this.updateTimer = setInterval(() => {
            this.loadAllStats();
            this.metrics.lastUpdate = Date.now();
        }, this.config.updateInterval);
    }
    
    pauseUpdates() {
        if (this.updateTimer) {
            clearInterval(this.updateTimer);
        }
    }
    
    resumeUpdates() {
        this.startRealTimeUpdates();
    }
    
    renderProcessingCounter() {
        const target = this.metrics.processing;
        const current = parseInt(this.elements.counter.textContent) || 0;
        
        // Lewati jika nilai sama dan bukan first load
        if (current === target && current !== 0) return;
        
        // Tambah animasi pulse jika nilai berubah (menghormati reduced motion)
        if (current !== target && !this.prefersReducedMotion) {
            this.elements.counter.classList.add('pulse-lampung');
            setTimeout(() => {
                this.elements.counter.classList.remove('pulse-lampung');
            }, 600);
        }
        
        // Animasi counter dengan easing
        this.animateCounter(this.elements.counter, current, target, 500);
    }
    
    renderUmkmCounter() {
        if (!this.elements.umkmCounter) return;
        
        const target = this.metrics.totalUmkm;
        const current = parseInt(this.elements.umkmCounter.textContent) || 0;
        
        // Tambah animasi pulse jika nilai berubah (menghormati reduced motion)
        if (current !== target && !this.prefersReducedMotion) {
            this.elements.umkmCounter.classList.add('pulse-lampung');
            setTimeout(() => {
                this.elements.umkmCounter.classList.remove('pulse-lampung');
            }, 600);
        }
        
        // Selalu animasi ke nilai target
        this.animateCounter(this.elements.umkmCounter, current, target, 1000);
    }
    
    animateCounter(element, start, end, duration) {
        const startTime = performance.now();
        const difference = end - start;
        
        const step = (currentTime) => {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Ease-out cubic
            const easeProgress = 1 - Math.pow(1 - progress, 3);
            const currentValue = Math.round(start + (difference * easeProgress));
            
            element.textContent = currentValue;
            
            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };
        
        requestAnimationFrame(step);
    }
    
    destroy() {
        this.pauseUpdates();
    }
}

// Inisialisasi saat DOM siap
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.liveDashboard = new LiveDashboard();
    });
} else {
    window.liveDashboard = new LiveDashboard();
}

// Bersihkan saat halaman ditutup
window.addEventListener('beforeunload', () => {
    if (window.liveDashboard) {
        window.liveDashboard.destroy();
    }
});

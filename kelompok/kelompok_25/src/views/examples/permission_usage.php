<!-- Contoh Penggunaan Permission di View -->

<!-- 1. Hide/Show Create Button -->
<div class="page-header">
    <h1>Daftar Bahan Baku</h1>
    <div class="actions">
        <?php if (has_permission('create_materials')): ?>
            <a href="<?= url('/materials/create') ?>" class="btn btn-primary">
                <svg class="w-4 h-4"><path d="M12 4v16m8-8H4"/></svg>
                Tambah Bahan Baku
            </a>
        <?php endif; ?>
        
        <?php if (has_permission('export_materials')): ?>
            <a href="<?= url('/materials/export') ?>" class="btn btn-secondary">
                <svg class="w-4 h-4"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3"/></svg>
                Export Excel
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- 2. Hide/Show Action Buttons di Table -->
<table class="table">
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Stok</th>
            <th>Harga</th>
            <?php if (has_any_permission(['edit_materials', 'delete_materials'])): ?>
                <th class="text-center">Aksi</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($materials as $material): ?>
            <tr>
                <td><?= e($material['code']) ?></td>
                <td><?= e($material['name']) ?></td>
                <td><?= number_format($material['stock']) ?></td>
                <td>Rp <?= number_format($material['price'], 0, ',', '.') ?></td>
                
                <?php if (has_any_permission(['edit_materials', 'delete_materials'])): ?>
                    <td class="text-center">
                        <div class="flex gap-2 justify-center">
                            <?php if (has_permission('edit_materials')): ?>
                                <a href="<?= url('/materials/edit/' . $material['id']) ?>" 
                                   class="btn btn-sm btn-warning"
                                   title="Edit">
                                    <svg class="w-4 h-4"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </a>
                            <?php endif; ?>
                            
                            <?php if (has_permission('delete_materials')): ?>
                                <button onclick="confirmDelete(<?= $material['id'] ?>)" 
                                        class="btn btn-sm btn-danger"
                                        title="Hapus">
                                    <svg class="w-4 h-4"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- 3. Hide Entire Section jika tidak punya permission -->
<?php if (has_permission('view_reports')): ?>
    <section class="card mt-6">
        <div class="card-header">
            <h3>Laporan Statistik</h3>
        </div>
        <div class="card-body">
            <!-- Report content here -->
            
            <?php if (has_permission('export_reports')): ?>
                <div class="mt-4">
                    <a href="<?= url('/reports/export') ?>" class="btn btn-primary">
                        Download Laporan
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<!-- 4. Show different content untuk role berbeda -->
<div class="dashboard-stats">
    <div class="stat-card">
        <h4>Total Bahan Baku</h4>
        <p class="stat-value"><?= $stats['total_materials'] ?></p>
    </div>
    
    <!-- Hanya show value untuk admin/manager -->
    <?php if (has_any_permission(['view_reports', 'export_reports'])): ?>
        <div class="stat-card">
            <h4>Total Nilai Stok</h4>
            <p class="stat-value">Rp <?= number_format($stats['total_value'], 0, ',', '.') ?></p>
        </div>
    <?php endif; ?>
    
    <!-- Staff hanya lihat jumlah, bukan nilai -->
    <?php if (!has_permission('view_reports') && has_permission('view_materials')): ?>
        <div class="stat-card">
            <h4>Status Stok</h4>
            <p class="stat-value"><?= $stats['low_stock_count'] ?> bahan menipis</p>
        </div>
    <?php endif; ?>
</div>

<!-- 5. Conditional Dropdown Actions -->
<div class="dropdown">
    <button class="btn-dropdown">Aksi ⋮</button>
    <div class="dropdown-menu">
        <a href="<?= url('/materials/view/' . $id) ?>">Detail</a>
        
        <?php if (has_permission('edit_materials')): ?>
            <a href="<?= url('/materials/edit/' . $id) ?>">Edit</a>
        <?php endif; ?>
        
        <?php if (has_permission('create_stock_in')): ?>
            <a href="<?= url('/stock-in/create?material=' . $id) ?>">Tambah Stok</a>
        <?php endif; ?>
        
        <?php if (has_permission('create_stock_out')): ?>
            <a href="<?= url('/stock-out/create?material=' . $id) ?>">Keluarkan Stok</a>
        <?php endif; ?>
        
        <?php if (has_permission('delete_materials')): ?>
            <hr>
            <a href="#" onclick="confirmDelete(<?= $id ?>)" class="text-danger">Hapus</a>
        <?php endif; ?>
    </div>
</div>

<!-- 6. Form dengan conditional fields -->
<form action="<?= url('/materials/store') ?>" method="POST">
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
    
    <div class="form-group">
        <label>Nama Bahan</label>
        <input type="text" name="name" required>
    </div>
    
    <div class="form-group">
        <label>Harga Satuan</label>
        <input type="number" name="price" required>
    </div>
    
    <!-- Hanya admin/manager yang bisa set min_stock -->
    <?php if (has_any_permission(['edit_materials', 'create_materials']) && !has_role('staff')): ?>
        <div class="form-group">
            <label>Minimum Stok</label>
            <input type="number" name="min_stock" value="10">
            <small class="text-muted">Sistem akan alert jika stok dibawah nilai ini</small>
        </div>
    <?php endif; ?>
    
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="<?= url('/materials') ?>" class="btn btn-secondary">Batal</a>
    </div>
</form>

<!-- 7. Alert/Notice berdasarkan permission -->
<?php if (has_permission('view_low_stock')): ?>
    <?php if ($low_stock_count > 0): ?>
        <div class="alert alert-warning">
            <svg class="w-5 h-5"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <div>
                <strong>Perhatian!</strong>
                Ada <?= $low_stock_count ?> bahan dengan stok menipis.
                <?php if (has_permission('view_reports')): ?>
                    <a href="<?= url('/reports/low-stock') ?>" class="alert-link">Lihat Detail</a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- 8. Tabs dengan permission check -->
<div class="tabs">
    <button class="tab active" data-tab="info">Informasi</button>
    
    <?php if (has_permission('view_stock_in')): ?>
        <button class="tab" data-tab="history-in">Riwayat Stok Masuk</button>
    <?php endif; ?>
    
    <?php if (has_permission('view_stock_out')): ?>
        <button class="tab" data-tab="history-out">Riwayat Stok Keluar</button>
    <?php endif; ?>
    
    <?php if (has_permission('view_reports')): ?>
        <button class="tab" data-tab="analytics">Analitik</button>
    <?php endif; ?>
</div>

<!-- 9. Badge status dengan info berbeda per role -->
<div class="material-status">
    <span class="badge badge-<?= $material['stock'] > $material['min_stock'] ? 'success' : 'danger' ?>">
        Stok: <?= number_format($material['stock']) ?> <?= $material['unit'] ?>
    </span>
    
    <?php if (has_permission('view_reports')): ?>
        <span class="badge badge-info">
            Nilai: Rp <?= number_format($material['stock'] * $material['price'], 0, ',', '.') ?>
        </span>
    <?php endif; ?>
</div>

<!-- 10. Quick Actions Widget -->
<?php if (has_any_permission(['create_materials', 'create_stock_in', 'create_stock_out'])): ?>
    <div class="quick-actions">
        <h3>Aksi Cepat</h3>
        <div class="action-buttons">
            <?php if (has_permission('create_materials')): ?>
                <a href="<?= url('/materials/create') ?>" class="action-btn">
                    <svg><path d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Bahan</span>
                </a>
            <?php endif; ?>
            
            <?php if (has_permission('create_stock_in')): ?>
                <a href="<?= url('/stock-in/create') ?>" class="action-btn">
                    <svg><path d="M12 4v16m0 0l-6-6m6 6l6-6"/></svg>
                    <span>Stok Masuk</span>
                </a>
            <?php endif; ?>
            
            <?php if (has_permission('create_stock_out')): ?>
                <a href="<?= url('/stock-out/create') ?>" class="action-btn">
                    <svg><path d="M12 20V4m0 0l6 6m-6-6l-6 6"/></svg>
                    <span>Stok Keluar</span>
                </a>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

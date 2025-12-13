<?php

class ExcelExporter {
    
    public static function exportStockReport($materials, $filename = null) {
        if (!$filename) {
            $filename = 'laporan_stok_' . date('Y-m-d') . '.xls';
        }
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; font-weight: bold; }';
        echo '.number { text-align: right; }';
        echo '.status-safe { background-color: #d4edda; color: #155724; }';
        echo '.status-warning { background-color: #fff3cd; color: #856404; }';
        echo '.status-danger { background-color: #f8d7da; color: #721c24; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<h2>Laporan Stok Terkini - ' . date('d/m/Y H:i:s') . '</h2>';
        
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Nama Bahan</th>';
        echo '<th>Kategori</th>';
        echo '<th>Stok Tersedia</th>';
        echo '<th>Stok Minimum</th>';
        echo '<th>Harga/Unit</th>';
        echo '<th>Total Nilai</th>';
        echo '<th>Status</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $no = 1;
        foreach ($materials as $material) {
            $status = $material['current_stock'] > $material['min_stock'] ? 'Aman' : 
                     ($material['current_stock'] > 0 ? 'Hampir Habis' : 'Perlu Restock');
            $statusClass = $status === 'Aman' ? 'status-safe' : 
                          ($status === 'Hampir Habis' ? 'status-warning' : 'status-danger');
            
            echo '<tr>';
            echo '<td class="number">' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($material['name']) . '</td>';
            echo '<td>' . htmlspecialchars($material['category_name'] ?? '-') . '</td>';
            echo '<td class="number">' . number_format($material['current_stock'], 0, ',', '.') . ' ' . $material['unit'] . '</td>';
            echo '<td class="number">' . number_format($material['min_stock'], 0, ',', '.') . ' ' . $material['unit'] . '</td>';
            echo '<td class="number">Rp ' . number_format($material['unit_price'], 0, ',', '.') . '</td>';
            echo '<td class="number">Rp ' . number_format($material['total_value'], 0, ',', '.') . '</td>';
            echo '<td class="' . $statusClass . '">' . $status . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
    
    public static function exportTransactionReport($transactions, $filename = null) {
        if (!$filename) {
            $filename = 'laporan_transaksi_' . date('Y-m-d') . '.xls';
        }
        
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        echo '<?xml version="1.0" encoding="UTF-8"?>';
        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<style>';
        echo 'table { border-collapse: collapse; width: 100%; }';
        echo 'th, td { border: 1px solid #000; padding: 8px; text-align: left; }';
        echo 'th { background-color: #f2f2f2; font-weight: bold; }';
        echo '.number { text-align: right; }';
        echo '.center { text-align: center; }';
        echo '.type-in { background-color: #d4edda; color: #155724; }';
        echo '.type-out { background-color: #fff3cd; color: #856404; }';
        echo '.type-adj { background-color: #cce5ff; color: #004085; }';
        echo '</style>';
        echo '</head>';
        echo '<body>';
        
        echo '<h2>Laporan Transaksi - ' . date('d/m/Y H:i:s') . '</h2>';
        
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Tanggal</th>';
        echo '<th>Jenis Transaksi</th>';
        echo '<th>Nama Bahan</th>';
        echo '<th>Jumlah</th>';
        echo '<th>Satuan</th>';
        echo '<th>Nilai (Rp)</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $no = 1;
        foreach ($transactions as $txn) {
            $typeText = $txn['type'] === 'stock_in' ? 'Stok Masuk' : 
                       ($txn['type'] === 'stock_out' ? 'Stok Keluar' : 'Penyesuaian');
            $typeClass = $txn['type'] === 'stock_in' ? 'type-in' : 
                        ($txn['type'] === 'stock_out' ? 'type-out' : 'type-adj');
            
            echo '<tr>';
            echo '<td class="number">' . $no++ . '</td>';
            echo '<td class="center">' . date('d/m/Y', strtotime($txn['date'])) . '</td>';
            echo '<td class="' . $typeClass . '">' . $typeText . '</td>';
            echo '<td>' . htmlspecialchars($txn['material_name']) . '</td>';
            echo '<td class="number">' . number_format($txn['quantity'], 0, ',', '.') . '</td>';
            echo '<td class="center">' . htmlspecialchars($txn['unit']) . '</td>';
            echo '<td class="number">' . number_format($txn['value'], 0, ',', '.') . '</td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        echo '</body>';
        echo '</html>';
        exit;
    }
}
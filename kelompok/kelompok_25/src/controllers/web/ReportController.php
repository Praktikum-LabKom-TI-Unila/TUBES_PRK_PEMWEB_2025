<?php

require_once __DIR__ . '/../../models/Material.php';
require_once __DIR__ . '/../../helpers/ExcelExporter.php';

class ReportController extends Controller {
    
    public function stockReport() {
        $materialModel = new Material();
        
        $search = $_GET['search'] ?? '';
        $categoryFilter = $_GET['category'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        
        $materials = $materialModel->getStockReport($search, $categoryFilter, $statusFilter);
        $summary = $materialModel->getStockSummary();
        $categories = $materialModel->getCategories();
        
        $this->view('reports/stock', [
            'materials' => $materials,
            'summary' => $summary,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $categoryFilter,
                'status' => $statusFilter
            ]
        ]);
    }

    public function exportExcel() {
        $materialModel = new Material();
        
        $search = $_GET['search'] ?? '';
        $categoryFilter = $_GET['category'] ?? '';
        $statusFilter = $_GET['status'] ?? '';
        
        $materials = $materialModel->getStockReport($search, $categoryFilter, $statusFilter);
        
        ExcelExporter::exportStockReport($materials);
    }

    public function transactionReport() {
        // Method untuk menampilkan laporan transaksi
        // Implementasi sesuai kebutuhan
    }

    public function exportTransactions() {
        try {
            require_once __DIR__ . '/../../models/Transaction.php';
            
            $transactionModel = new Transaction();
            
            $type = $_GET['type'] ?? 'all';
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';
            
            $transactions = $transactionModel->getTransactionReport($type, $startDate, $endDate);
            
            // Simple Excel export without helper
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="laporan_transaksi_' . date('Y-m-d') . '.xls"');
            header('Cache-Control: max-age=0');
            
            echo '<table border="1">';
            echo '<tr><th>Tanggal</th><th>Jenis</th><th>Nama Bahan</th><th>Jumlah</th><th>Nilai</th></tr>';
            
            foreach ($transactions as $txn) {
                $typeText = $txn['type'] === 'stock_in' ? 'Stok Masuk' : 
                           ($txn['type'] === 'stock_out' ? 'Stok Keluar' : 'Penyesuaian');
                
                echo '<tr>';
                echo '<td>' . date('Y-m-d', strtotime($txn['date'])) . '</td>';
                echo '<td>' . $typeText . '</td>';
                echo '<td>' . htmlspecialchars($txn['material_name']) . '</td>';
                echo '<td>' . number_format($txn['quantity'], 0, ',', '.') . ' ' . $txn['unit'] . '</td>';
                echo '<td>Rp ' . number_format($txn['value'], 0, ',', '.') . '</td>';
                echo '</tr>';
            }
            
            echo '</table>';
            exit;
            
        } catch (Exception $e) {
            header('Content-Type: text/html');
            echo '<h3>Error: ' . $e->getMessage() . '</h3>';
            echo '<p>File: ' . $e->getFile() . '</p>';
            echo '<p>Line: ' . $e->getLine() . '</p>';
            exit;
        }
    }
}

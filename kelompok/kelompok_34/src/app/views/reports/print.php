<?php
// File: src/app/views/reports/print.php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Penjualan - <?= APP_NAME ?></title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background: white;
      color: #333;
    }
    
    .header {
      text-align: center;
      margin-bottom: 30px;
      padding-bottom: 20px;
      border-bottom: 3px solid #2563eb;
    }
    
    .header h1 {
      color: #2563eb;
      margin-bottom: 5px;
    }
    
    .header p {
      color: #666;
    }
    
    .info-section {
      margin-bottom: 30px;
      background: #f3f4f6;
      padding: 15px;
      border-radius: 8px;
    }
    
    .info-row {
      display: flex;
      justify-content: space-between;
      margin-bottom: 8px;
    }
    
    .info-label {
      font-weight: bold;
      color: #374151;
    }
    
    .summary-cards {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 30px;
    }
    
    .summary-card {
      background: #eff6ff;
      border-left: 4px solid #2563eb;
      padding: 15px;
      border-radius: 8px;
    }
    
    .summary-card h3 {
      color: #1e40af;
      font-size: 14px;
      margin-bottom: 8px;
    }
    
    .summary-card .value {
      font-size: 24px;
      font-weight: bold;
      color: #1e3a8a;
    }
    
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }
    
    thead {
      background: #2563eb;
      color: white;
    }
    
    th, td {
      padding: 12px;
      text-align: left;
      border-bottom: 1px solid #e5e7eb;
    }
    
    th {
      font-weight: bold;
    }
    
    tbody tr:hover {
      background: #f9fafb;
    }
    
    .text-right {
      text-align: right;
    }
    
    .footer {
      text-align: center;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 2px solid #e5e7eb;
      color: #6b7280;
      font-size: 12px;
    }
    
    .no-print {
      position: fixed;
      top: 20px;
      right: 20px;
      background: #2563eb;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 14px;
      font-weight: bold;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .no-print:hover {
      background: #1d4ed8;
    }
    
    @media print {
      .no-print {
        display: none;
      }
      
      body {
        padding: 0;
      }
      
      table {
        page-break-inside: auto;
      }
      
      tr {
        page-break-inside: avoid;
        page-break-after: auto;
      }
    }
  </style>
</head>
<body>
  <button class="no-print" onclick="window.print()">🖨️ Print / Save PDF</button>

  <div class="header">
    <h1><?= APP_NAME ?></h1>
    <p>Laporan Penjualan</p>
  </div>

  <div class="info-section">
    <div class="info-row">
      <span class="info-label">Periode:</span>
      <span><?= date('d/m/Y', strtotime($filter['start_date'])) ?> - <?= date('d/m/Y', strtotime($filter['end_date'])) ?></span>
    </div>
    <div class="info-row">
      <span class="info-label">Tanggal Cetak:</span>
      <span><?= date('d/m/Y H:i:s') ?></span>
    </div>
    <?php if (!empty($filter['product_id'])): ?>
      <div class="info-row">
        <span class="info-label">Filter Produk:</span>
        <span>Ya</span>
      </div>
    <?php endif; ?>
  </div>

  <div class="summary-cards">
    <div class="summary-card">
      <h3>Total Transaksi</h3>
      <div class="value"><?= number_format($summary['total_transactions']) ?></div>
    </div>
    <div class="summary-card">
      <h3>Total Item Terjual</h3>
      <div class="value"><?= number_format($summary['total_items']) ?></div>
    </div>
    <div class="summary-card">
      <h3>Total Pendapatan</h3>
      <div class="value">Rp <?= number_format($summary['total_revenue'], 0, ',', '.') ?></div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Tanggal</th>
        <th>Kasir</th>
        <th>Produk</th>
        <th class="text-right">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($transactions)): ?>
        <tr>
          <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
            Tidak ada transaksi ditemukan
          </td>
        </tr>
      <?php else: ?>
        <?php foreach ($transactions as $transaction): ?>
          <tr>
            <td>#<?= $transaction['id'] ?></td>
            <td><?= date('d/m/Y H:i', strtotime($transaction['created_at'])) ?></td>
            <td><?= htmlspecialchars($transaction['cashier_name']) ?></td>
            <td style="font-size: 12px;"><?= htmlspecialchars($transaction['products']) ?></td>
            <td class="text-right"><strong>Rp <?= number_format($transaction['total_amount'], 0, ',', '.') ?></strong></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer">
    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Semua hak dilindungi.</p>
    <p>Dokumen ini dicetak secara otomatis dari sistem</p>
  </div>
</body>
</html>
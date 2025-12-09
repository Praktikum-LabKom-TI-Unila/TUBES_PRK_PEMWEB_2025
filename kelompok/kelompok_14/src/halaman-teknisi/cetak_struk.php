<?php
$serviceId = isset($_GET['serviceId']) ? intval($_GET['serviceId']) : null;
$customerName = isset($_GET['customerName']) ? htmlspecialchars($_GET['customerName']) : '';
$itemName = isset($_GET['itemName']) ? htmlspecialchars($_GET['itemName']) : '';
$diagnosisDesc = isset($_GET['diagnosisDesc']) ? htmlspecialchars($_GET['diagnosisDesc']) : '';
$additionalDetails = isset($_GET['additionalDetails']) ? htmlspecialchars($_GET['additionalDetails']) : '';
$components = isset($_GET['components']) ? json_decode($_GET['components'], true) : [];
$laborCost = isset($_GET['laborCost']) ? floatval($_GET['laborCost']) : 0;

function formatCurrency($value) {
  return 'Rp ' . number_format($value, 0, ',', '.');
}

$componentTotal = 0;
foreach ($components as $comp) {
  $componentTotal += isset($comp['cost']) ? $comp['cost'] : 0;
}
$total = $componentTotal + $laborCost;
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Struk Layanan Perbaikan - FixTrack</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>@media print { .print-button { display: none !important; } body { background: white !important; } .container { box-shadow: none !important; } }</style>
</head>
<body class="bg-gray-100 p-5 print:bg-white print:p-0">
  <div class="max-w-2xl mx-auto bg-white p-10 rounded-lg shadow-md print:shadow-none">
    <!-- Header -->
    <div class="text-center border-b-4 border-gray-800 pb-4 mb-6">
      <h1 class="text-2xl font-bold">STRUK LAYANAN PERBAIKAN</h1>
      <p class="text-xs text-gray-600">FixTrack Service Center</p>
      <p class="text-xs text-gray-600">Tanggal: <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <!-- Info Pelanggan -->
    <div class="mb-5">
      <div class="flex justify-between mb-2 text-sm">
        <span class="font-bold">Pelanggan:</span>
        <span><?php echo $customerName ?: '(Tidak ada)'; ?></span>
      </div>
      <div class="flex justify-between mb-2 text-sm">
        <span class="font-bold">Barang:</span>
        <span><?php echo $itemName ?: '(Tidak ada)'; ?></span>
      </div>
      <div class="flex justify-between text-sm">
        <span class="font-bold">No. Servis:</span>
        <span>#<?php echo str_pad($serviceId, 3, '0', STR_PAD_LEFT); ?></span>
      </div>
    </div>

    <!-- Diagnosa -->
    <div class="border-t-2 border-b-2 border-gray-800 py-3 mb-5">
      <h3 class="font-bold text-sm mb-2">Deskripsi Diagnosa:</h3>
      <p class="text-xs leading-relaxed text-gray-700 mb-2"><?php echo $diagnosisDesc ?: 'Tidak ada deskripsi diagnosa'; ?></p>
      <?php if ($additionalDetails): ?>
        <h3 class="font-bold text-sm mb-2">Catatan Tambahan:</h3>
        <p class="text-xs leading-relaxed text-gray-700"><?php echo $additionalDetails; ?></p>
      <?php endif; ?>
    </div>

    <!-- Komponen & Biaya -->
    <div class="mb-5">
      <h3 class="font-bold text-sm mb-3">Komponen & Biaya:</h3>
      <?php if (!empty($components) && count($components) > 0): ?>
        <table class="w-full text-xs">
          <thead class="border-b border-gray-600">
            <tr>
              <th class="text-left py-2 font-bold">Nama Komponen</th>
              <th class="text-right py-2 font-bold">Harga</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($components as $comp): ?>
              <tr class="border-b border-gray-300">
                <td class="py-1"><?php echo htmlspecialchars($comp['name'] ?? 'Komponen'); ?></td>
                <td class="text-right py-1"><?php echo formatCurrency($comp['cost'] ?? 0); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="text-center py-2 text-gray-400 italic text-xs">Tidak ada komponen</div>
      <?php endif; ?>
    </div>

    <!-- Ringkasan Biaya -->
    <div class="mt-4 text-xs">
      <div class="flex justify-between mb-1">
        <span>Total Komponen:</span>
        <span><?php echo formatCurrency($componentTotal); ?></span>
      </div>
      <div class="flex justify-between mb-2">
        <span>Biaya Jasa:</span>
        <span><?php echo formatCurrency($laborCost); ?></span>
      </div>
      <div class="flex justify-between border-t-2 border-gray-800 pt-2 font-bold text-sm">
        <span>TOTAL KESELURUHAN:</span>
        <span><?php echo formatCurrency($total); ?></span>
      </div>
    </div>

    <!-- Footer -->
    <div class="text-center mt-6 pt-5 border-t border-gray-300 text-xs text-gray-600">
      <p>Terima kasih telah menggunakan layanan kami</p>
      <p>FixTrack © <?php echo date('Y'); ?> - Semua Hak Dilindungi</p>
    </div>

    <!-- Print Button -->
    <button onclick="window.print()" class="print-button block mx-auto mt-5 px-8 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded text-sm font-medium cursor-pointer">Cetak Struk</button>
  </div>

  <script>
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('autoprint') === '1') {
      window.addEventListener('load', () => window.print());
    }
  </script>
</body>
</html>

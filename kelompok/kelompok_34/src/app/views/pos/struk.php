<!DOCTYPE html>
<html>

<head>
    <title>Struk #<?= $data['transaksi']['id'] ?></title>
    <style>
        body {
            font-family: monospace;
            font-size: 12px;
            max-width: 300px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
        }

        .total {
            border-top: 1px dashed #000;
            margin-top: 10px;
            padding-top: 10px;
            font-weight: bold;
            text-align: right;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body onload="window.print()">

    <div class="header">
        <h2><?= APP_NAME ?></h2>
        <p>Jl. Kampus Unila No. 1</p>
        <p>Struk ID: #<?= $data['transaksi']['id'] ?></p>
        <p>Kasir: <?= $data['kasir'] ?></p>
        <p><?= $data['transaksi']['created_at'] ?></p>
    </div>

    <div class="items">
        <?php foreach ($data['items'] as $item): ?>
            <div class="item">
                <span><?= $item['name'] ?> (x<?= $item['qty'] ?>)</span>
                <span><?= number_format($item['subtotal']) ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="total">
        TOTAL: Rp <?= number_format($data['transaksi']['total_amount']) ?>
    </div>

    <center class="no-print" style="margin-top: 20px;">
        <a href="<?= BASE_URL ?>/pos">Kembali ke Kasir</a>
    </center>

</body>

</html>
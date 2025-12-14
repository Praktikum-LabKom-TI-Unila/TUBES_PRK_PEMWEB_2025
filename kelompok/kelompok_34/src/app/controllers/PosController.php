<?php

class PosController extends Controller
{
    public function __construct()
    {
        // Cek login dulu (menggunakan helper yang sudah ada)
        requireSeller();
    }

    public function index()
    {
        $data['title'] = 'Kasir - ' . APP_NAME;
        // Ambil data produk lewat Model
        $data['products'] = $this->model('Product')->getAll();

        // Tampilkan View
        $this->view('pos/index', $data);
    }

    // ... function index() yang lama biarkan saja ...

    public function checkout()
    {
        // Set header to JSON
        header('Content-Type: application/json');

        // 1. Terima Data dari Javascript
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data['cart'])) {
            echo json_encode(['status' => 'error', 'message' => 'Keranjang kosong']);
            exit;
        }

        // 2. Simpan Transaksi
        $transModel = $this->model('Transaction');
        $prodModel = $this->model('Product');

        try {
            $paymentMethod = $data['payment_method'];

            // Check if using Midtrans
            if ($paymentMethod === 'MIDTRANS') {
                // Load Midtrans helper
                require_once BASE_PATH . '/src/app/helpers/midtrans_helper.php';
                require_once BASE_PATH . '/src/app/config/midtrans.php';

                // Generate unique order ID
                $orderId = 'ORDER-' . time() . '-' . $_SESSION['user']['id'];

                // Prepare items for Midtrans
                $items = [];
                foreach ($data['cart'] as $item) {
                    $items[] = [
                        'id' => $item['id'],
                        'price' => (int) $item['price'],
                        'quantity' => (int) $item['qty'],
                        'name' => $item['name']
                    ];
                }

                // Create Snap token
                $snapResult = MidtransHelper::createSnapToken([
                    'order_id' => $orderId,
                    'gross_amount' => $data['total_final'],
                    'customer_name' => $_SESSION['user']['name'],
                    'customer_email' => $_SESSION['user']['email'] ?? 'customer@lokapos.com',
                    'items' => $items
                ]);

                if ($snapResult['status'] === 'error') {
                    echo json_encode(['status' => 'error', 'message' => $snapResult['message']]);
                    exit;
                }

                // Create transaction with Midtrans data
                $transId = $transModel->create([
                    'total_final' => $data['total_final'],
                    'payment_method' => 'MIDTRANS',
                    'pay_amount' => $data['total_final'],
                    'change_amount' => 0,
                    'midtrans_order_id' => $orderId,
                    'midtrans_snap_token' => $snapResult['snap_token']
                ]);

                // Add cart details
                foreach ($data['cart'] as $item) {
                    $transModel->createDetail($transId, $item);
                    $prodModel->reduceStock($item['id'], $item['qty']);
                }

                echo json_encode([
                    'status' => 'success',
                    'payment_type' => 'midtrans',
                    'snap_token' => $snapResult['snap_token'],
                    'transId' => $transId
                ]);
                exit;
            }

            // Regular payment (CASH)
            // A. Buat Struk Baru (Mengirim data lengkap ke Model)
            $transId = $transModel->create([
                'total_final'    => $data['total_final'],
                'payment_method' => $data['payment_method'],
                'pay_amount'     => $data['pay_amount'],
                'change_amount'  => $data['change_amount']
            ]);

            // B. Masukkan isi keranjang (Kode ini TETAP SAMA seperti sebelumnya)
            foreach ($data['cart'] as $item) {
                $transModel->createDetail($transId, $item);
                $prodModel->reduceStock($item['id'], $item['qty']);
            }

            echo json_encode(['status' => 'success', 'transId' => $transId]);
        } catch (Exception $e) {
            // Tampilkan error asli biar gampang debugging
            echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }

    public function paymentFinish()
    {
        $data['title'] = 'Pembayaran Selesai - ' . APP_NAME;
        $this->view('pos/payment-finish', $data);
    }

    public function updatePaymentSuccess()
    {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (!isset($data['transId'])) {
            echo json_encode(['status' => 'error', 'message' => 'Transaction ID required']);
            exit;
        }

        $transModel = $this->model('Transaction');
        $transaction = $transModel->getTransactionById($data['transId']);

        if ($transaction && isset($transaction['midtrans_order_id'])) {
            $transModel->updateMidtransStatus(
                $transaction['midtrans_order_id'],
                'settlement',
                'midtrans'
            );
        }

        echo json_encode(['status' => 'success']);
        exit;
    }

    public function midtransNotification()
    {
        header('Content-Type: application/json');

        $json = file_get_contents('php://input');
        $notification = json_decode($json, true);

        if (!$notification) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid notification']);
            exit;
        }

        $transModel = $this->model('Transaction');
        $orderId = $notification['order_id'];
        $status = $notification['transaction_status'];
        $paymentType = $notification['payment_type'] ?? null;

        if (in_array($status, ['capture', 'settlement'])) {
            $transModel->updateMidtransStatus($orderId, 'settlement', $paymentType);
        } elseif (in_array($status, ['cancel', 'deny', 'expire'])) {
            $transModel->updateMidtransStatus($orderId, $status, $paymentType);
        }

        echo json_encode(['status' => 'success']);
        exit;
    }

    public function struk($id)
    {
        // 1. Ambil Data Transaksi berdasarkan ID
        $data['transaksi'] = $this->model('Transaction')->getTransactionById(($id));
        $data['items'] = $this->model('Transaction')->getTransactionItems(($id));
        $data['kasir'] = $_SESSION['user']['name']; // Nama kasir dari session

        // 2. Tampilkan View khusus Struk (Tanpa Header/Footer biar rapi pas diprint)
        require_once BASE_PATH . '/src/app/views/pos/struk.php';
    }
}

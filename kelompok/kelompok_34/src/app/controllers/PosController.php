<?php

class PosController extends Controller
{
    public function __construct()
    {
        // Cek login dulu (menggunakan helper yang sudah ada)
        requireAuth();
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

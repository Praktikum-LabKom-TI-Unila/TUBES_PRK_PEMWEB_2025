<?php


class ReportController
{
  private $reportModel;

  public function __construct()
  {
    // Check if user is logged in and is admin
    session_start();
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
      header('Location: ' . BASE_URL . '/auth/login');
      exit;
    }

    require_once BASE_PATH . '/src/app/models/Report.php';
    $this->reportModel = new Report();
  }

  // Halaman utama laporan penjualan
  public function index()
  {
    $filter = [
      'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
      'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
      'product_id' => $_GET['product_id'] ?? ''
    ];

    $transactions = $this->reportModel->getTransactionsWithFilter($filter);
    $products = $this->reportModel->getAllProducts();
    $summary = $this->reportModel->getSalesSummary($filter);

    $data = [
      'title' => 'Laporan Penjualan - ' . APP_NAME,
      'transactions' => $transactions,
      'products' => $products,
      'filter' => $filter,
      'summary' => $summary
    ];

    $this->view('reports/index', $data);
  }

  // Dashboard admin dengan statistik
  public function dashboard()
  {
    $stats = [
      'today' => $this->reportModel->getTodayStats(),
      'month' => $this->reportModel->getMonthStats(),
      'year' => $this->reportModel->getYearStats(),
      'top_products' => $this->reportModel->getTopProducts(5),
      'recent_transactions' => $this->reportModel->getRecentTransactions(10),
      'monthly_chart' => $this->reportModel->getMonthlyChartData(),
      'daily_chart' => $this->reportModel->getDailyChartData()
    ];

    $data = [
      'title' => 'Dashboard Admin - ' . APP_NAME,
      'stats' => $stats
    ];

    $this->view('reports/dashboard', $data);
  }

  // Print/Export laporan ke PDF
  public function print()
  {
    $filter = [
      'start_date' => $_GET['start_date'] ?? date('Y-m-01'),
      'end_date' => $_GET['end_date'] ?? date('Y-m-d'),
      'product_id' => $_GET['product_id'] ?? ''
    ];

    $transactions = $this->reportModel->getTransactionsWithFilter($filter);
    $summary = $this->reportModel->getSalesSummary($filter);

    $data = [
      'transactions' => $transactions,
      'filter' => $filter,
      'summary' => $summary
    ];

    $this->view('reports/print', $data);
  }

  // Helper untuk load view
  private function view($view, $data = [])
  {
    extract($data);
    require_once BASE_PATH . '/src/app/views/' . $view . '.php';
  }
}
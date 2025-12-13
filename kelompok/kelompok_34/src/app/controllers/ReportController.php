<?php

class ReportController extends Controller
{
  private $reportModel;

  public function __construct()
  {
    requireAdmin();
    $this->reportModel = $this->model('Report');
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

    $this->plainView('reports/print', $data);
  }
}

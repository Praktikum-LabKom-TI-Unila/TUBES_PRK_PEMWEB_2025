<?php

/**
 * Material API Controller
 * Menangani endpoint API untuk manajemen material
 */

require_once ROOT_PATH . '/core/Controller.php';
require_once ROOT_PATH . '/core/Response.php';
require_once ROOT_PATH . '/core/Auth.php';
require_once ROOT_PATH . '/models/Material.php';
require_once ROOT_PATH . '/models/Category.php';

class MaterialApiController extends Controller
{
    private $materialModel;
    private $categoryModel;

    public function __construct()
    {
        AuthMiddleware::check();
        
        $this->materialModel = new Material();
        $this->categoryModel = new Category();
    }

    /**
     * GET /api/materials
     * Get all materials
     */
    public function index()
    {
        try {
            $materials = $this->materialModel->getAll();
            
            // Add stock status to each material
            foreach ($materials as &$material) {
                if ($material['current_stock'] > $material['min_stock']) {
                    $material['stock_status'] = 'Aman';
                } elseif ($material['current_stock'] > 0) {
                    $material['stock_status'] = 'Hampir Habis';
                } else {
                    $material['stock_status'] = 'Habis';
                }
            }

            Response::success('Data material berhasil diambil', [
                'data' => $materials,
                'total' => count($materials)
            ]);

        } catch (Exception $e) {
            Response::error('Gagal mengambil data material', [], 500);
        }
    }

    /**
     * GET /api/materials/:id
     * Get material detail
     */
    public function show($id)
    {
        try {
            $material = $this->materialModel->findById($id);

            if (!$material) {
                Response::error('Material tidak ditemukan', [], 404);
                return;
            }

            // Add stock status
            if ($material['current_stock'] > $material['min_stock']) {
                $material['stock_status'] = 'Aman';
            } elseif ($material['current_stock'] > 0) {
                $material['stock_status'] = 'Hampir Habis';
            } else {
                $material['stock_status'] = 'Habis';
            }

            Response::success('Detail material berhasil diambil', [
                'data' => $material
            ]);

        } catch (Exception $e) {
            Response::error('Gagal mengambil detail material', [], 500);
        }
    }

    /**
     * GET /api/materials/report/stock
     * Get stock report
     */
    public function stockReport()
    {
        try {
            $search = $_GET['search'] ?? '';
            $categoryFilter = $_GET['category'] ?? '';
            $statusFilter = $_GET['status'] ?? '';

            $materials = $this->materialModel->getStockReport($search, $categoryFilter, $statusFilter);

            Response::success('Laporan stok berhasil diambil', [
                'data' => $materials,
                'total' => count($materials)
            ]);

        } catch (Exception $e) {
            Response::error('Gagal mengambil laporan stok', [], 500);
        }
    }

    /**
     * GET /api/materials/summary
     * Get stock summary
     */
    public function summary()
    {
        try {
            $summary = $this->materialModel->getStockSummary();

            Response::success('Ringkasan stok berhasil diambil', [
                'data' => $summary
            ]);

        } catch (Exception $e) {
            Response::error('Gagal mengambil ringkasan stok', [], 500);
        }
    }

    /**
     * GET /api/materials/categories
     * Get available categories for materials
     */
    public function categories()
    {
        try {
            $categories = $this->materialModel->getCategories();

            Response::success('Daftar kategori berhasil diambil', [
                'data' => $categories,
                'total' => count($categories)
            ]);

        } catch (Exception $e) {
            Response::error('Gagal mengambil daftar kategori', [], 500);
        }
    }
}

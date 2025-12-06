<?php
declare(strict_types=1);

namespace Controllers;

use Core\HttpException;
use Core\Request;
use Core\Response;
use Models\Product;
use Models\ProductCategory;

final class ProductController
{
    public function __construct(
        private readonly Product $products = new Product(),
        private readonly ProductCategory $categories = new ProductCategory()
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return Response::json(['data' => $this->products->all()]);
    }

    public function show(Request $request, array $params = []): Response
    {
        $product = $this->products->find((int) $params['id']);
        if ($product === null) {
            throw new HttpException('Produk tidak ditemukan', 404);
        }

        return Response::json(['data' => $product]);
    }

    public function store(Request $request, array $params = []): Response
    {
        $payload = $this->validate($request->body());

        $category = $this->categories->find($payload['category_id']);
        if ($category === null || (int) $category['is_active'] === 0) {
            throw new HttpException('Kategori tidak tersedia atau tidak aktif.', 422);
        }

        $created = $this->products->create($payload);

        return Response::json(['message' => 'Produk dibuat', 'data' => $created], 201);
    }

    public function update(Request $request, array $params = []): Response
    {
        $existing = $this->products->find((int) $params['id']);
        if ($existing === null) {
            throw new HttpException('Produk tidak ditemukan', 404);
        }

        $payload = $this->validate($request->body());

        $category = $this->categories->find($payload['category_id']);
        if ($category === null || (int) $category['is_active'] === 0) {
            throw new HttpException('Kategori tidak tersedia atau tidak aktif.', 422);
        }

        $updated = $this->products->update((int) $params['id'], $payload);

        return Response::json(['message' => 'Produk diperbarui', 'data' => $updated]);
    }

    public function destroy(Request $request, array $params = []): Response
    {
        $existing = $this->products->find((int) $params['id']);
        if ($existing === null) {
            throw new HttpException('Produk tidak ditemukan', 404);
        }

        $this->products->delete((int) $params['id']);

        return Response::json(['message' => 'Produk dihapus']);
    }

    private function validate(array $body): array
    {
        $code = trim((string) ($body['code'] ?? ''));
        $name = trim((string) ($body['name'] ?? ''));
        $categoryId = $body['category_id'] ?? null;
        $unit = trim((string) ($body['unit'] ?? ''));

        if ($code === '' || $name === '' || $unit === '' || !is_numeric($categoryId)) {
            throw new HttpException('code, name, unit, dan category_id wajib diisi.', 422);
        }

        $currentStock = isset($body['current_stock']) ? (float) $body['current_stock'] : 0.0;
        $minStock = isset($body['min_stock']) ? (int) $body['min_stock'] : 0;
        $currentPrice = isset($body['current_price']) ? (float) $body['current_price'] : null;

        if ($currentPrice === null || $currentPrice < 0) {
            throw new HttpException('current_price wajib diisi dan >= 0.', 422);
        }

        return [
            'code' => $code,
            'name' => $name,
            'category_id' => (int) $categoryId,
            'unit' => $unit,
            'current_stock' => $currentStock,
            'min_stock' => $minStock,
            'current_price' => $currentPrice,
            'is_active' => filter_var($body['is_active'] ?? true, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
        ];
    }
}

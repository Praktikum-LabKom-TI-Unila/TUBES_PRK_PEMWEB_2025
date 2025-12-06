<?php
declare(strict_types=1);

namespace Controllers;

use Core\HttpException;
use Core\Request;
use Core\Response;
use Models\Product;
use Models\ProductBatch;

final class ProductBatchController
{
    public function __construct(
        private readonly ProductBatch $batches = new ProductBatch(),
        private readonly Product $products = new Product()
    ) {
    }

    public function index(Request $request, array $params = []): Response
    {
        return Response::json(['data' => $this->batches->all()]);
    }

    public function show(Request $request, array $params = []): Response
    {
        $batch = $this->batches->find((int) $params['id']);
        if ($batch === null) {
            throw new HttpException('Batch tidak ditemukan', 404);
        }

        return Response::json(['data' => $batch]);
    }

    public function store(Request $request, array $params = []): Response
    {
        $payload = $this->validate($request->body());
        $product = $this->products->find($payload['product_id']);
        if ($product === null || (int) $product['is_active'] === 0) {
            throw new HttpException('Produk tidak ditemukan atau tidak aktif.', 422);
        }

        $created = $this->batches->create($payload);

        return Response::json(['message' => 'Batch dibuat', 'data' => $created], 201);
    }

    public function update(Request $request, array $params = []): Response
    {
        $existing = $this->batches->find((int) $params['id']);
        if ($existing === null) {
            throw new HttpException('Batch tidak ditemukan', 404);
        }

        $payload = $this->validate($request->body());
        $product = $this->products->find($payload['product_id']);
        if ($product === null || (int) $product['is_active'] === 0) {
            throw new HttpException('Produk tidak ditemukan atau tidak aktif.', 422);
        }

        $updated = $this->batches->update((int) $params['id'], $payload, (int) $existing['product_id']);

        return Response::json(['message' => 'Batch diperbarui', 'data' => $updated]);
    }

    public function destroy(Request $request, array $params = []): Response
    {
        $existing = $this->batches->find((int) $params['id']);
        if ($existing === null) {
            throw new HttpException('Batch tidak ditemukan', 404);
        }

        $this->batches->delete((int) $params['id']);

        return Response::json(['message' => 'Batch dihapus']);
    }

    private function validate(array $body): array
    {
        $productId = $body['product_id'] ?? null;
        $batchCode = trim((string) ($body['batch_code'] ?? ''));
        $purchaseDate = trim((string) ($body['purchase_date'] ?? ''));
        $purchasePrice = isset($body['purchase_price']) ? (float) $body['purchase_price'] : null;
        $quantity = isset($body['quantity']) ? (float) $body['quantity'] : null;
        $quantityUsed = isset($body['quantity_used']) ? (float) $body['quantity_used'] : 0.0;
        $quantityRemaining = $body['quantity_remaining'] ?? null;
        $sellingPrice = isset($body['selling_price']) ? (float) $body['selling_price'] : null;

        if (!is_numeric($productId) || $batchCode === '' || $purchaseDate === '' || $purchasePrice === null || $quantity === null || $quantity <= 0 || $sellingPrice === null) {
            throw new HttpException('Data batch tidak lengkap.', 422);
        }

        if (!$this->isValidDate($purchaseDate)) {
            throw new HttpException('purchase_date harus berformat YYYY-MM-DD.', 422);
        }

        if ($quantityUsed < 0) {
            throw new HttpException('quantity_used tidak boleh negatif.', 422);
        }

        if ($quantityRemaining === null) {
            $quantityRemaining = $quantity - $quantityUsed;
        } else {
            $quantityRemaining = (float) $quantityRemaining;
        }

        if ($quantityRemaining < 0 || $quantityRemaining > $quantity) {
            throw new HttpException('quantity_remaining harus antara 0 dan quantity.', 422);
        }

        return [
            'product_id' => (int) $productId,
            'batch_code' => $batchCode,
            'purchase_date' => $purchaseDate,
            'purchase_price' => $purchasePrice,
            'quantity' => $quantity,
            'quantity_used' => $quantityUsed,
            'quantity_remaining' => $quantityRemaining,
            'selling_price' => $sellingPrice,
            'is_active' => filter_var($body['is_active'] ?? true, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? true,
        ];
    }

    private function isValidDate(string $value): bool
    {
        $date = date_create_from_format('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }
}

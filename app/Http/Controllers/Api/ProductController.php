<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\Api\ProductService;

class ProductController extends Controller
{
    use ApiResponse;

    public function __construct(
        protected ProductService $productService,
    ) {}

    /**
     * GET /api/products — list products of the authenticated seller.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->seller) {
            return $this->error('Forbidden', 403);
        }

        $products = $this->productService->listForSeller($user);

        $items = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'price' => $p->price,
                'category' => $p->category,
                'photo_path' => $p->photo_path,
                'photo_url' => $p->photo_path ? url('storage/' . $p->photo_path) : null,
                'seller_id' => $p->seller_id,
            ];
        });

        return $this->success(['products' => $items], 'Success', 200);
    }

    /**
     * POST /api/products — add product (seller only).
     */
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $path = $request->file('photo')->store('products', 'public');
        $product = $this->productService->createForSeller($request->user(), $data, $path);

        return $this->success([
            'product' => $this->formatProduct($product),
        ], 'Product added successfully', 201);
    }

    /**
     * PUT /api/products/{id} — update product (seller only).
     */
    public function update(UpdateProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }

        $data = $request->validated();
        $newPhotoPath = null;
        if ($request->hasFile('photo')) {
            $newPhotoPath = $request->file('photo')->store('products', 'public');
        }
        $product = $this->productService->updateForSeller($request->user(), $product, $data, $newPhotoPath);

        return $this->success(['product' => $this->formatProduct($product)], 'Product updated successfully', 200);
    }

    /**
     * DELETE /api/products/{id} — delete product (seller only).
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        if (!$user || !$user->seller) {
            return $this->error('You are not registered as a seller', 403);
        }

        $product = Product::find($id);
        if (!$product) {
            return $this->error('Product not found', 404);
        }

        try {
            $this->productService->deleteForSeller($user, $product);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        return $this->success(null, 'Product deleted successfully', 200);
    }

    protected function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'category' => $product->category,
            'photo_path' => $product->photo_path,
            'photo_url' => $product->photo_path ? url('storage/' . $product->photo_path) : null,
            'seller_id' => $product->seller_id,
        ];
    }
}

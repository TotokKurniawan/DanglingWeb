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
                'is_active' => $p->is_active,
                'seller_id' => $p->seller_id,
            ];
        });

        return $this->success(['products' => $items], 'Success', 200);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     summary="Add a new product (Seller only)",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","price","photo"},
     *                 @OA\Property(property="name", type="string", example="Baju Koko"),
     *                 @OA\Property(property="description", type="string", example="Baju muslim modern"),
     *                 @OA\Property(property="price", type="number", example=100000),
     *                 @OA\Property(property="stock", type="integer", example=10),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="photo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product added successfully"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=403, description="Forbidden")
     * )
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

        $this->authorize('manage', $product);

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

        $this->authorize('manage', $product);

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
            'is_active' => $product->is_active,
            'seller_id' => $product->seller_id,
        ];
    }

    /**
     * PATCH /api/products/{id}/toggle-active — toggle aktif/nonaktif produk.
     */
    public function toggleActive(Request $request, $id)
    {
        $product = Product::find($id);
        if (! $product) {
            return $this->error('Product not found', 404);
        }

        try {
            $product = $this->productService->toggleActive($request->user(), $product);
        } catch (\RuntimeException $e) {
            if ($e->getMessage() === 'Forbidden') {
                return $this->error('Forbidden', 403);
            }
            return $this->error($e->getMessage(), 422);
        }

        return $this->success([
            'product' => $this->formatProduct($product),
        ], $product->is_active ? 'Produk diaktifkan' : 'Produk dinonaktifkan', 200);
    }
    /**
     * GET /api/products/search — pencarian produk lintas seller.
     * Query params: q, category_id, price_min, price_max, per_page
     */
    public function search(Request $request)
    {
        $perPage = min((int) $request->input('per_page', 20), 50);

        $query = Product::active()
            ->with(['seller', 'categoryRelation'])
            ->whereHas('seller', function ($q) {
                $q->where('is_online', true)->where('is_suspended', false);
            });

        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('price_min')) {
            $query->where('price', '>=', (int) $request->input('price_min'));
        }

        if ($request->filled('price_max')) {
            $query->where('price', '<=', (int) $request->input('price_max'));
        }

        $products = $query->orderBy('name')->paginate($perPage);

        return $this->success([
            'products'   => collect($products->items())->map(fn ($p) => $this->formatProduct($p)),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
            ],
        ], 'Search results', 200);
    }
}

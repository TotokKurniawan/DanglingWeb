<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/products — list products of the authenticated seller.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->seller) {
            return $this->error('Forbidden', 403);
        }

        $products = Product::where('id_pedagang', $user->seller->id)
            ->orderByDesc('created_at')
            ->get();

        $items = $products->map(function ($p) {
            return [
                'id' => $p->id,
                'nama_produk' => $p->nama_produk,
                'harga_produk' => $p->harga_produk,
                'kategori_produk' => $p->kategori_produk,
                'foto' => $p->foto,
                'foto_url' => $p->foto ? url('storage/' . $p->foto) : null,
                'id_pedagang' => $p->id_pedagang,
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
        $path = $request->file('foto')->store('produk', 'public');
        $product = Product::create([
            'nama_produk' => $data['nama_produk'],
            'harga_produk' => (int) $data['harga_produk'],
            'kategori_produk' => $data['kategori_produk'],
            'foto' => $path,
            'id_pedagang' => $request->user()->seller->id,
        ]);

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
        if (isset($data['nama_produk'])) {
            $product->nama_produk = $data['nama_produk'];
        }
        if (isset($data['harga_produk'])) {
            $product->harga_produk = (int) $data['harga_produk'];
        }
        if (isset($data['kategori_produk'])) {
            $product->kategori_produk = $data['kategori_produk'];
        }
        if ($request->hasFile('foto')) {
            if ($product->foto) {
                Storage::disk('public')->delete($product->foto);
            }
            $product->foto = $request->file('foto')->store('produk', 'public');
        }
        $product->save();

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
        if ((int) $product->id_pedagang !== (int) $user->seller->id) {
            return $this->error('Forbidden', 403);
        }

        if ($product->foto) {
            Storage::disk('public')->delete($product->foto);
        }
        $product->delete();

        return $this->success(null, 'Product deleted successfully', 200);
    }

    protected function formatProduct(Product $product): array
    {
        return [
            'id' => $product->id,
            'nama_produk' => $product->nama_produk,
            'harga_produk' => $product->harga_produk,
            'kategori_produk' => $product->kategori_produk,
            'foto' => $product->foto,
            'foto_url' => $product->foto ? url('storage/' . $product->foto) : null,
            'id_pedagang' => $product->id_pedagang,
        ];
    }
}

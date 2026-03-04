<?php

namespace App\Services\Api;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    public function listForSeller(User $sellerUser): Collection
    {
        $seller = $sellerUser->seller;
        if (! $seller) {
            return collect();
        }

        return Product::where('seller_id', $seller->id)
            ->orderByDesc('created_at')
            ->get();
    }

    public function createForSeller(User $sellerUser, array $data, string $storedPhotoPath, array $imagesPaths = []): Product
    {
        $seller = $sellerUser->seller;
        if (! $seller) {
            throw new \RuntimeException('You are not registered as a seller');
        }

        $product = Product::create([
            'name'           => $data['name'],
            'price'          => (int) $data['price'],
            'original_price' => array_key_exists('original_price', $data) && $data['original_price'] !== null ? (int) $data['original_price'] : null,
            'category'       => $data['category'],
            'photo_path'     => $storedPhotoPath,
            'seller_id'      => $seller->id,
        ]);

        foreach ($imagesPaths as $index => $path) {
            $product->images()->create([
                'image_path' => $path,
                'is_primary' => $index === 0,
            ]);
        }

        return $product;
    }

    public function updateForSeller(User $sellerUser, Product $product, array $data, ?string $newPhotoPath = null, array $newImagesPaths = []): Product
    {
        $seller = $sellerUser->seller;
        if (! $seller || (int) $product->seller_id !== (int) $seller->id) {
            throw new \RuntimeException('Forbidden');
        }

        if (isset($data['name'])) {
            $product->name = $data['name'];
        }
        if (isset($data['price'])) {
            $product->price = (int) $data['price'];
        }
        if (array_key_exists('original_price', $data)) {
            $product->original_price = $data['original_price'] !== null ? (int) $data['original_price'] : null;
        }
        if (isset($data['category'])) {
            $product->category = $data['category'];
        }
        if ($newPhotoPath !== null) {
            if ($product->photo_path) {
                Storage::disk('public')->delete($product->photo_path);
            }
            $product->photo_path = $newPhotoPath;
        }

        $product->save();

        if (!empty($newImagesPaths)) {
            foreach ($newImagesPaths as $path) {
                $product->images()->create([
                    'image_path' => $path,
                    'is_primary' => false,
                ]);
            }
        }

        return $product;
    }

    public function deleteForSeller(User $sellerUser, Product $product): void
    {
        $seller = $sellerUser->seller;
        if (! $seller || (int) $product->seller_id !== (int) $seller->id) {
            throw new \RuntimeException('Forbidden');
        }

        if ($product->photo_path) {
            Storage::disk('public')->delete($product->photo_path);
        }

        $product->delete();
    }

    /**
     * Toggle aktif/nonaktif produk.
     */
    public function toggleActive(User $sellerUser, Product $product): Product
    {
        $seller = $sellerUser->seller;
        if (! $seller || (int) $product->seller_id !== (int) $seller->id) {
            throw new \RuntimeException('Forbidden');
        }

        $product->is_active = ! $product->is_active;
        $product->save();

        return $product;
    }
}

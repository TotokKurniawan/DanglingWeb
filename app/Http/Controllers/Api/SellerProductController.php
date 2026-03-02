<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SellerProductController extends Controller
{
    use ApiResponse;

    public function getAllSellers(Request $request)
    {
        try {
            $sellers = Seller::where('status', 'online')
                ->with(['products'])
                ->get();

            if ($sellers->isEmpty()) {
                return $this->success(['items' => []], 'No online sellers', 200);
            }

            $items = [];
            foreach ($sellers as $seller) {
                foreach ($seller->products as $product) {
                    $items[] = [
                        'id' => $product->id,
                        'name' => $product->name,
                        'price' => $product->price,
                        'category' => $product->category,
                        'product_photo_url' => $product->photo_path ? url('storage/' . $product->photo_path) : null,
                        'seller_id' => $seller->id,
                        'seller_photo_url' => $seller->photo_path ? url('storage/' . $seller->photo_path) : null,
                    ];
                }
            }

            return $this->success(['items' => $items], 'Success', 200);
        } catch (\Exception $e) {
            Log::error('getAllSellers: ' . $e->getMessage());
            return $this->error('Server error', 500);
        }
    }

    public function getSellerById(Request $request, $id)
    {
        $seller = Seller::with('products')->find($id);
        if (!$seller) {
            return $this->error('Seller not found', 404);
        }

        $data = $seller->toArray();
        if (isset($data['photo_path']) && $data['photo_path']) {
            $data['photo_url'] = url('storage/' . $data['photo_path']);
        }
        foreach ($data['products'] ?? [] as $i => $p) {
            if (!empty($p['photo_path'])) {
                $data['products'][$i]['photo_url'] = url('storage/' . $p['photo_path']);
            }
        }

        return $this->success(['seller' => $data], 'Success', 200);
    }
}

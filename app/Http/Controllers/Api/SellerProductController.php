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
                        'nama_produk' => $product->nama_produk,
                        'harga_produk' => $product->harga_produk,
                        'kategori_produk' => $product->kategori_produk,
                        'foto_produk' => $product->foto ? url('storage/' . $product->foto) : null,
                        'id_pedagang' => $seller->id,
                        'foto_pedagang' => $seller->foto ? url('storage/' . $seller->foto) : null,
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
        if (isset($data['foto']) && $data['foto']) {
            $data['foto_url'] = url('storage/' . $data['foto']);
        }
        foreach ($data['products'] ?? [] as $i => $p) {
            if (!empty($p['foto'])) {
                $data['products'][$i]['foto_url'] = url('storage/' . $p['foto']);
            }
        }

        return $this->success(['seller' => $data], 'Success', 200);
    }
}

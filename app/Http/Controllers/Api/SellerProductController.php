<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Seller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SellerProductController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/sellers
     *
     * Query params:
     *   lat     float  - latitude buyer (opsional)
     *   lng     float  - longitude buyer (opsional)
     *   radius  float  - radius pencarian dalam km (default: 10, opsional)
     *   sort    string - 'distance' | 'rating' | 'name' (default: 'distance' jika ada koordinat, else 'name')
     *   page    int    - nomor halaman (default: 1)
     *   per_page int   - jumlah per halaman (default: 15, max: 50)
     */
    public function getAllSellers(Request $request)
    {
        try {
            $lat     = $request->filled('lat') ? (float) $request->lat : null;
            $lng     = $request->filled('lng') ? (float) $request->lng : null;
            $radius  = $request->filled('radius') ? (float) $request->radius : 10.0;
            $sort    = $request->input('sort', ($lat !== null && $lng !== null) ? 'distance' : 'name');
            $perPage = min((int) $request->input('per_page', 15), 50);

            $query = Seller::where('is_online', true)
                ->where('is_suspended', false)
                ->with(['products' => fn ($q) => $q->where('is_active', true)]);

            // Filter radius dengan Haversine di level database
            if ($lat !== null && $lng !== null) {
                $haversine = '(6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                ))';

                $query->selectRaw("sellers.*, {$haversine} AS distance", [$lat, $lng, $lat])
                      ->whereNotNull('latitude')
                      ->whereNotNull('longitude')
                      ->having('distance', '<=', $radius);

                if ($sort === 'distance') {
                    $query->orderBy('distance');
                }
            } else {
                $query->select('sellers.*');
            }

            // Sorting
            match ($sort) {
                'rating' => $query->orderByDesc('rating_average')->orderByDesc('rating_count'),
                'name'   => $query->orderBy('store_name'),
                default  => null, // distance sudah di-order di atas
            };

            $sellers = $query->paginate($perPage);

            $items = [];
            foreach ($sellers as $seller) {
                $sellerData = [
                    'id'             => $seller->id,
                    'user_id'        => $seller->user_id,
                    'store_name'     => $seller->store_name,
                    'address'        => $seller->address,
                    'phone'          => $seller->phone,
                    'photo_url'      => $seller->photo_path ? url('storage/' . $seller->photo_path) : null,
                    'rating_average' => $seller->rating_average,
                    'rating_count'   => $seller->rating_count,
                    'open_time'      => $seller->open_time,
                    'close_time'     => $seller->close_time,
                    'latitude'       => $seller->latitude,
                    'longitude'      => $seller->longitude,
                    'distance_km'    => isset($seller->distance) ? round($seller->distance, 2) : null,
                    'products'       => $seller->products->map(fn ($p) => [
                        'id'        => $p->id,
                        'name'      => $p->name,
                        'price'     => $p->price,
                        'category'  => $p->category,
                        'photo_url' => $p->photo_path ? url('storage/' . $p->photo_path) : null,
                    ]),
                ];
                $items[] = $sellerData;
            }

            return $this->success([
                'items'       => $items,
                'pagination'  => [
                    'current_page' => $sellers->currentPage(),
                    'per_page'     => $sellers->perPage(),
                    'total'        => $sellers->total(),
                    'last_page'    => $sellers->lastPage(),
                ],
            ], 'Success', 200);
        } catch (\Exception $e) {
            Log::error('getAllSellers: ' . $e->getMessage());
            return $this->error('Server error', 500);
        }
    }

    /**
     * GET /api/sellers/{id}
     */
    public function getSellerById(Request $request, $id)
    {
        $seller = Seller::with('products')->find($id);
        if (!$seller) {
            return $this->error('Seller not found', 404);
        }

        $data = [
            'id'             => $seller->id,
            'user_id'        => $seller->user_id,
            'store_name'     => $seller->store_name,
            'address'        => $seller->address,
            'phone'          => $seller->phone,
            'photo_url'      => $seller->photo_path ? url('storage/' . $seller->photo_path) : null,
            'is_online'      => $seller->is_online,
            'rating_average' => $seller->rating_average,
            'rating_count'   => $seller->rating_count,
            'open_time'      => $seller->open_time,
            'close_time'     => $seller->close_time,
            'latitude'       => $seller->latitude,
            'longitude'      => $seller->longitude,
            'products'       => $seller->products->map(fn ($p) => [
                'id'        => $p->id,
                'name'      => $p->name,
                'price'     => $p->price,
                'category'  => $p->category,
                'photo_url' => $p->photo_path ? url('storage/' . $p->photo_path) : null,
            ]),
        ];

        return $this->success(['seller' => $data], 'Success', 200);
    }
}

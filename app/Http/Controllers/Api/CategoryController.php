<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\Category;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/categories — daftar semua kategori aktif.
     */
    public function index()
    {
        $categories = Category::active()
            ->orderBy('name')
            ->get(['id', 'name', 'icon']);

        return $this->success(['categories' => $categories], 'Success', 200);
    }
}

<?php

namespace App\Http\Controllers\Web;

use App\Models\Seller;
use App\Models\Buyer;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $pembelis = Buyer::all();
        $pedagangs = Seller::all();
        return view('landingpage.home', compact('pembelis', 'pedagangs'));
    }

    public function features()
    {
        return view('landingpage.features');
    }
}

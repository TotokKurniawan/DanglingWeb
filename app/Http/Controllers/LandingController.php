<?php

namespace App\Http\Controllers;

use App\Models\Pedagang;
use App\Models\Pembeli;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function home()
    {
        // Mengambil seluruh data pembeli dan pedagang dari masing-masing tabel
        $pembelis = Pembeli::all();
        $pedagangs = Pedagang::all();

        // Melewatkan data pembeli dan pedagang ke view
        return view('landingpage.home', compact('pembelis', 'pedagangs'));
    }
}

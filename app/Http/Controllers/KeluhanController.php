<?php

namespace App\Http\Controllers;

use App\Http\Requests\KeluhanRequest;
use App\Models\Keluhan;
use Illuminate\Http\Request;

class KeluhanController extends Controller
{
    public function tambahkeluhan(KeluhanRequest $request)
    {
        // Simpan keluhan ke database
        Keluhan::create(attributes: [
            'deskripsi' => $request->deskripsi,  // Ini adalah deskripsi keluhan
            'rating' => $request->rating,       // Ini adalah rating dari form
            'id_pembeli' => $request->nama,     // Nama dari pembeli (id)
            'id_pedagang' => $request->pedagang // Nama pedagang (id)
        ]);
        session()->flash('success', 'Data berhasil Disimpan');

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Keluhan berhasil disimpan.');
    }
}

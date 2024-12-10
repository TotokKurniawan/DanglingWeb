<?php

namespace App\Http\Controllers;

use App\Models\Pedagang;
use Illuminate\Http\Request;

class PedagangController extends Controller
{
    public function updateStatus(Request $request)
    {
        // Validasi request
        $request->validate([
            'id' => 'required|exists:pedagangs,id', // Memastikan 'id' ada dalam tabel 'pedagang'
        ]);

        // Temukan item berdasarkan ID
        $item = Pedagang::find($request->id); // Pastikan 'Pedagang' adalah model yang benar

        if ($item) {
            // Toggle status antara 'offline' dan 'online'
            $item->status = $item->status === 'offline' ? 'online' : 'offline'; // Ganti 'status' dengan nama kolom yang sesuai
            $item->save();

            return redirect()->back()->with('message', 'Status updated successfully');
        }

        return redirect()->back()->with('error', 'Item not found');
    }
}

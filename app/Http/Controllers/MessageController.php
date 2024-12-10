<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    /**
     * Menampilkan semua pesan.
     */
    public function index()
    {
        $messages = Message::with(['pembeli', 'pedagang'])->get();
        return response()->json($messages, 200);
    }

    /**
     * Menyimpan pesan baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_pembeli' => 'required|exists:pembelis,id',
            'id_pedagang' => 'required|exists:pedagangs,id',
            'message' => 'required|string',
        ]);

        $message = Message::create($validated);

        return response()->json([
            'message' => 'Pesan berhasil dibuat',
            'data' => $message,
        ], 201);
    }

    /**
     * Menampilkan detail pesan berdasarkan ID.
     */
    public function show($id)
    {
        $message = Message::with(['pembeli', 'pedagang'])->find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        return response()->json($message, 200);
    }

    /**
     * Mengupdate pesan berdasarkan ID.
     */
    public function update(Request $request, $id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        $validated = $request->validate([
            'message' => 'required|string',
        ]);

        $message->update($validated);

        return response()->json([
            'message' => 'Pesan berhasil diperbarui',
            'data' => $message,
        ], 200);
    }

    /**
     * Menghapus pesan berdasarkan ID.
     */
    public function destroy($id)
    {
        $message = Message::find($id);

        if (!$message) {
            return response()->json(['message' => 'Pesan tidak ditemukan'], 404);
        }

        $message->delete();

        return response()->json(['message' => 'Pesan berhasil dihapus'], 200);
    }
}

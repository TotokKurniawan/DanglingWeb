<?php

namespace App\Http\Controllers;

use DB;
use Log;
use Exception;
use App\Models\User;
use App\Models\Pedagang;
use App\Models\Pembeli;
use App\Models\history;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class Apicontroller extends Controller{
    public function login(Request $request){
    // Validasi input
    $request->validate([
        'email' => 'required|email',
        'password' => 'required|min:6',
    ]);

    $user = User::where('email', $request->email)->first();

    if ($user && Hash::check($request->password, $user->password)) {

        DB::table('oauth_access_tokens')
            ->where('user_id', $user->id)
            ->update(['revoked' => true]);

        $token = $user->createToken('token')->accessToken;

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'token' => $token,
        ]);
    } else {
        return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
    }
    }
    public function sign_up(Request $request)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 400);
        }

        // Buat user baru dengan role 'pembeli' secara default
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'pembeli',
        ]);

        // Kembalikan response sukses dengan data user baru (tanpa password)
        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 201);
    }
    public function logout(Request $request){
    $user = Auth::user();
    $user->tokens->each(function ($token) {
        $token->delete();
    });

    return response()->json(['message' => 'Successfully logged out']);
    }
    public function upgradeToSeller(Request $request)
    {
        Log::info('Data request:', $request->all());
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'namaToko' => 'required|string',
            'telfon' => 'required|string',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ambil data user berdasarkan id_user
        $user = User::find($request->user_id);

        // Simpan data pedagang
        $pedagang = new Pedagang();
        $pedagang->namaToko = $request->namaToko;
        $pedagang->telfon = $request->telfon;
        $pedagang->alamat = $request->alamat;

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filePath = $file->store('pedagang', 'public');
            $pedagang->foto = $filePath;
        }

        $pedagang->status = 'online';
        $pedagang->user_id = $user->id;
        $pedagang->save();

        return response()->json(['success' => true]);
    }
    public function getStoreStatus(Request $request)
    {
        try {
            // Mengambil user yang sedang login
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated',
                ], 401);
            }

            // Simulasi status toko dari database
            $storeStatus = $user->store->is_online ?? false; // Kolom is_online

            return response()->json([
                'success' => true,
                'isOnline' => $storeStatus,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status toko',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request){
    $request->validate([
        'user_id' => 'required|integer',
        'status' => 'required|in:online,offline',
    ]);

    $store = Pedagang::where('user_id', $request->user_id)->first();

    if (!$store) {
        return response()->json(['message' => 'Toko tidak ditemukan'], 404);
    }

    // Update status
    $store->status = $request->status;
    $store->save();

    return response()->json(['success' => true, 'message' => 'Status toko diperbarui'], 200);
    }
    public function tambahProduk(Request $request)
{
    // Ambil user dari token
    $user = auth()->user();

    // Pastikan user memiliki data pedagang
    $pedagang = $user->pedagang;
    if (!$pedagang) {
        return response()->json([
            'success' => false,
            'message' => 'Anda belum terdaftar sebagai pedagang.',
        ], 403);
    }

    // Validasi input
    $validatedData = $request->validate([
        'nama_produk' => 'required|string',
        'harga_produk' => 'required|numeric',
        'kategori_produk' => 'required|string',
        'foto' => 'required|image',
    ]);

    // Buat produk baru
    $produk = new Produk([
        'nama_produk' => $validatedData['nama_produk'],
        'harga_produk' => $validatedData['harga_produk'],
        'kategori_produk' => $validatedData['kategori_produk'],
        'id_pedagang' => $pedagang->id, // Ambil ID pedagang dari user yang login
    ]);

    // Simpan file foto
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('produk', 'public');
        $produk->foto = $path;
    }

    $produk->save();

    return response()->json([
        'success' => true,
        'message' => 'Produk berhasil ditambahkan.',
    ], 201);
    }
    public function orderHistory()
    {
        $user = Auth::user();

        // Jika tidak ada pengguna yang terautentikasi, kembalikan error
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak terautentikasi.'], 401);
        }

        // Cek apakah pengguna adalah pembeli
        if ($user->pembeli) {
            $idPembeli = $user->pembeli->id;

            // Mengambil histori pesanan berdasarkan id pembeli
            $historys = History::where('id_pembeli', $idPembeli)->get();

            if ($historys->isEmpty()) {
                return response()->json(['message' => 'Histori pesanan pembeli tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'role' => 'pembeli',
                'data' => $historys
            ]);
        }

        // Cek apakah pengguna adalah pedagang
        if ($user->pedagang) {
            $idPedagang = $user->pedagang->id;

            // Mengambil histori pesanan berdasarkan id pedagang
            $historys = History::where('id_pedagang', $idPedagang)->get();

            if ($historys->isEmpty()) {
                return response()->json(['message' => 'Histori pesanan pedagang tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'role' => 'pedagang',
                'data' => $historys
            ]);
        }

        // Jika pengguna tidak memiliki peran pedagang atau pembeli
        return response()->json(['message' => 'Peran pengguna tidak valid.'], 403);
    }
    public function tampilSeluruhPedagang(Request $request)
{
    try {
        // Ambil data pedagang yang sedang online
        $pedagangs = Pedagang::where('status', 'online')
            ->with(['produk']) // Pastikan relasi produk dimuat
            ->get();

        if ($pedagangs->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada pedagang yang online'
            ], 200);
        }

        // Format data produk dari setiap pedagang
        $formattedData = [];
        foreach ($pedagangs as $pedagang) {
            foreach ($pedagang->produk as $produk) {
                $formattedData[] = [
                    'id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'hargaProduk' => $produk->harga_produk,
                    'kategori_produk' => $produk->kategori_produk,
                    'fotoProduk' => $produk->foto ? url('storage/' . $produk->foto) : null,
                    'id_pedagang' => $pedagang->id,
                    'fotoPedagang' => $pedagang->foto ? url('storage/' . $pedagang->foto) : null,
                    // 'latitude' => $pedagang->latitude,
                    // 'longitude' => $pedagang->longitude,
                ];
            }
        }


        return response()->json([
            'message' => 'Data pedagang online berhasil diambil',
            'data' => $formattedData,
        ], 200);
    } catch (\Exception $e) {
        Log::error('Error in tampilSeluruhPedagang: ' . $e->getMessage());
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    public function tampilPedagangBerdasarkanID($id)
    {
        $pedagang = Pedagang::find($id);

        if (!$pedagang) {
            return response()->json(['message' => 'Pedagang tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Data pedagang berhasil diambil',
            'data' => $pedagang
        ], 200);
    }

    public function updateLocation(Request $request, $id)
{
    try {
        $pedagang = Pedagang::findOrFail($id);
        $pedagang->latitude = $request->latitude;
        $pedagang->longitude = $request->longitude;
        $pedagang->save();

        return response()->json([
            'message' => 'Lokasi pedagang berhasil diperbarui'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
    }

    public function pesanan(Request $request)
    {
        // Ambil parameter id_pedagang dari request
        $id_pedagang = $request->input('id_pedagang');

        // Buat query untuk mengambil data berdasarkan id_pedagang dan status "menunggu"
        $query = history::query();

        // Jika id_pedagang diberikan, tambahkan filter untuk id_pedagang
        if ($id_pedagang) {
            $query->where('id_pedagang', $id_pedagang);
        }

        // Tambahkan filter untuk status "menunggu"
        $query->where('status', 'menunggu');

        // Ambil hasil query
        $histories = $query->get();

        // Mengembalikan data dalam format JSON
        return response()->json($histories);
    }
    public function tolakStatus(Request $request, $id)
    {
        try {
            // Validasi input dari request
            $validated = $request->validate([
                'alasanTolak' => 'required|string|max:255', // pastikan alasanTolak ada dan berupa string
            ]);

            // Cari data history berdasarkan ID
            $history = History::find($id);

            if (!$history) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Ubah status menjadi 'Tolak' dan simpan alasanTolak
            $history->status = 'Tolak';
            $history->alasanTolak = $validated['alasanTolak']; // Menyimpan alasanTolak
            $history->save();

            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            // Menampilkan pesan error yang lebih detail
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
    public function terimaStatus($id)
    {
        try {
            // Cari data history berdasarkan ID
            $history = history::find($id);

            if (!$history) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Ubah status menjadi 'Diterima'
            $history->status = 'Diterima';
            $history->save();

            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan'], 500);
        }
    }
    public function selesaiStatus($id)
    {
        try {
            // Cari data history berdasarkan ID
            $history = history::find($id);

            if (!$history) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Ubah status menjadi 'Diterima'
            $history->status = 'Selesai';
            $history->save();

            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan'], 500);
        }
    }
    public function batalStatus($id)
    {
        try {
            // Cari data history berdasarkan ID
            $history = history::find($id);

            if (!$history) {
                return response()->json(['message' => 'Data tidak ditemukan'], 404);
            }

            // Ubah status menjadi 'Diterima'
            $history->status = 'Dibatalkan';
            $history->save();

            return response()->json([
                'message' => 'Status berhasil diperbarui',
                'data' => $history
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan'], 500);
        }
    }
    public function updateProfilePembeli(Request $request, $id)
    {
        // Cari pembeli berdasarkan ID
        $pembeli = Pembeli::find($id);

        if (!$pembeli) {
            return response()->json(['message' => 'Pembeli tidak ditemukan'], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'telfon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Jika ada file foto baru
        ]);

        // Jika ada file foto baru, upload dan update
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto_pembelis'), $fileName);

            // Hapus file foto lama jika ada
            if ($pembeli->foto && file_exists(public_path('uploads/foto_pembelis/' . $pembeli->foto))) {
                unlink(public_path('uploads/foto_pembelis/' . $pembeli->foto));
            }

            $pembeli->foto = $fileName;
        }

        // Update data pembeli
        $pembeli->nama = $validated['nama'];
        $pembeli->telfon = $validated['telfon'];
        $pembeli->alamat = $validated['alamat'];
        $pembeli->save();

        return response()->json([
            'message' => 'Data pembeli berhasil diperbarui',
            'data' => $pembeli,
        ], 200);
    }
    public function updateProfilePedagang(Request $request, $id)
    {
        // Cari pedagang berdasarkan ID
        $pedagang = Pedagang::find($id);

        if (!$pedagang) {
            return response()->json(['message' => 'Pedagang tidak ditemukan'], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'telfon' => 'required|string|max:15',
            'alamat' => 'required|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Jika ada file foto baru
        ]);

        // Jika ada file foto baru, upload dan update
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/foto_pedagangs'), $fileName);

            // Hapus file foto lama jika ada
            if ($pedagang->foto && file_exists(public_path('uploads/foto_pedagangs/' . $pedagang->foto))) {
                unlink(public_path('uploads/foto_pedagangs/' . $pedagang->foto));
            }

            $pedagang->foto = $fileName;
        }

        // Update data pedagang
        $pedagang->nama = $validated['nama'];
        $pedagang->telfon = $validated['telfon'];
        $pedagang->alamat = $validated['alamat'];
        $pedagang->status = 'online'; //
        $pedagang->save();

        return response()->json([
            'message' => 'Profil pedagang berhasil diperbarui',
            'data' => $pedagang,
        ], 200);
    }

    public function Transaksi(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'bentuk_pembayaran' => 'required|string|max:255',
            'id_pembeli' => 'required|exists:pembelis,id',
            'id_pedagang' => 'required|exists:pedagangs,id',
        ]);

        // Buat data history baru
        $history = new history();
        $history->status = 'Menunggu';
        $history->bentuk_pembayaran = $validated['bentuk_pembayaran'];
        $history->id_pembeli = $validated['id_pembeli'];
        $history->id_pedagang = $validated['id_pedagang'];
        $history->save();

        return response()->json([
            'message' => 'Data history berhasil ditambahkan',
            'data' => $history,
        ], 201);
    }


}

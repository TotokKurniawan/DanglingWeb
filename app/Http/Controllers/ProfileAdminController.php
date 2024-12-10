<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileAdminController extends Controller
{
    public function Profile(Request $request)
    {
        // Mengambil email dari session
        $email = session('user_email');

        // Mengambil pengguna berdasarkan email
        $user = \App\Models\User::where('email', $email)->first();

        // Mengembalikan tampilan dengan data pengguna
        return view('admin.profile', compact('user'));
    }
    public function updateprofiladmin(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ambil pengguna dari database
        $user = User::findOrFail($id);

        // Update data pengguna
        $user->nama = $request->nama;
        $user->email = $request->email;

        // Jika password diisi, simpan tanpa hashing
        if ($request->filled('password')) {
            $user->password = $request->password; // Simpan password dalam bentuk plain text
        }

        // Jika ada foto baru yang diupload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete('public/foto_mitra/' . basename($user->foto)); // Ubah path di sini
            }

            // Simpan foto baru
            $fotoPath = $request->file('foto')->store('public/foto_mitra'); // Simpan foto di public/foto_mitra
            $user->foto = $fotoPath; // Simpan path foto di database
            session()->flash('success', 'Data berhasil diubah');
        }

        // Simpan perubahan ke database
        $user->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('Profile', $user->id)->with('success', 'Profile updated successfully');
    }
    public function updateprofiloperator(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Ambil pengguna dari database
        $user = User::findOrFail($id);

        // Update data pengguna
        $user->nama = $request->nama;
        $user->email = $request->email;

        // Jika password diisi, simpan tanpa hashing
        if ($request->filled('password')) {
            $user->password = $request->password; // Simpan password dalam bentuk plain text
        }

        // Jika ada foto baru yang diupload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto) {
                Storage::delete('public/foto_mitra/' . basename($user->foto)); // Ubah path di sini
            }

            // Simpan foto baru
            $fotoPath = $request->file('foto')->store('public/foto_mitra'); // Simpan foto di public/foto_mitra
            $user->foto = $fotoPath; // Simpan path foto di database
            session()->flash('success', 'Data berhasil diubah');
        }

        // Simpan perubahan ke database
        $user->save();

        // Redirect kembali dengan pesan sukses
        return redirect()->route('profileOperator', parameters: $user->id)->with('success', 'Profile updated successfully');
    }
}

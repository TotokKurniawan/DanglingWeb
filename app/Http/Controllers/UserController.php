<?php

namespace App\Http\Controllers;

use App\Http\Requests\TambahOperatorRequest;
use App\Http\Requests\UpdateOperatorRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function tambahoperator(TambahOperatorRequest $request)
    {
        // Proses upload foto jika ada
        $fotoPath = null; // Inisialisasi fotoPath

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('public/foto_mitra');
        }

        // Buat pengguna baru
        $user = new User();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = $request->password; // Simpan password tanpa hashing
        $user->role = $request->role;
        $user->foto = $fotoPath; // Menyimpan path foto
        $user->save();

        session()->flash('success', 'Data berhasil Disimpan');

        // Redirect dengan pesan sukses
        return redirect()->route('DataAdmin')->with('success', 'Mitra berhasil ditambahkan.');
    }
    public function update(UpdateOperatorRequest $request, $id)
    {
        // Temukan user berdasarkan ID
        $user = User::findOrFail($id);

        // Update nama
        $user->nama = $request->nama;

        // Update password jika diisi
        if ($request->filled('password')) {
            $user->password = $request->password; // Simpan password tanpa hashing
        }

        // Update role
        $user->role = $request->role;

        // Simpan perubahan ke database
        $user->save();

        session()->flash('success', 'Data berhasil Diubah');

        // Redirect atau kembalikan respons sesuai kebutuhan
        return redirect()->route('DataAdmin')->with('success', 'Data operator berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', value: 'Data Operator berhasil dihapus!');
    }
}

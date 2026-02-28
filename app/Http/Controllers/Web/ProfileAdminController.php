<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileAdminController extends Controller
{
    public function showProfile(Request $request)
    {
        $email = session('user_email');
        $user = User::where('email', $email)->first();
        return view('admin.profile', compact('user'));
    }

    public function updateAdminProfile(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete('public/foto_mitra/' . basename($user->foto));
            }
            $user->foto = $request->file('foto')->store('public/foto_mitra');
            session()->flash('success', 'Data updated successfully.');
        }
        $user->save();
        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updateOperatorProfile(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:6',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->hasFile('foto')) {
            if ($user->foto) {
                Storage::delete('public/foto_mitra/' . basename($user->foto));
            }
            $user->foto = $request->file('foto')->store('public/foto_mitra');
            session()->flash('success', 'Data updated successfully.');
        }
        $user->save();
        return redirect()->route('operator.profile.show', $user->id)->with('success', 'Profile updated successfully.');
    }
}

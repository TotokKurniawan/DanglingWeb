<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Http\Requests\Web\TambahOperatorRequest;
use App\Http\Requests\Web\UpdateOperatorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function storeOperator(TambahOperatorRequest $request)
    {
        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('public/foto_mitra');
        }

        $user = new User();
        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = $request->role;
        $user->foto = $fotoPath;
        $user->save();

        session()->flash('success', 'Data saved successfully.');
        return redirect()->route('admin.operators.index')->with('success', 'Operator added successfully.');
    }

    public function updateOperator(UpdateOperatorRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $user->nama = $request->nama;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        $user->role = $request->role;
        $user->save();

        session()->flash('success', 'Data updated successfully.');
        return redirect()->route('admin.operators.index')->with('success', 'Operator updated successfully.');
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Operator deleted successfully.');
    }
}

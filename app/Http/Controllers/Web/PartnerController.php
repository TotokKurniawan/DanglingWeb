<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\MitraRequest;
use App\Http\Requests\Web\UpdateMitraRequest;
use App\Models\Partner;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    public function index()
    {
        $mitras = Partner::paginate(10);
        return view('admin.mitra', compact('mitras'));
    }

    public function createForm()
    {
        return view('admin.form.tambah');
    }

    public function store(MitraRequest $request)
    {
        Partner::create($request->validated());
        session()->flash('success', 'Data saved successfully.');
        return redirect()->route('partners.index')->with('success', 'Partner added successfully.');
    }

    public function update(UpdateMitraRequest $request, $id)
    {
        $partner = Partner::findOrFail($id);
        $partner->update($request->validated());
        session()->flash('success', 'Data updated successfully.');
        return redirect()->route('partners.index')->with('success', 'Partner updated successfully.');
    }

    public function destroy($id)
    {
        Partner::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Partner deleted successfully.');
    }
}

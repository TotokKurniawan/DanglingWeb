<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\StorePartnerRequest;
use App\Http\Requests\Web\UpdatePartnerRequest;
use App\Models\Partner;
use App\Services\Web\PartnerService;

class PartnerController extends Controller
{
    public function __construct(
        protected PartnerService $partnerService,
    ) {}

    public function index()
    {
        $partners = $this->partnerService->paginate(10);
        return view('admin.mitra', ['mitras' => $partners]);
    }

    public function createForm()
    {
        return view('admin.form.tambah');
    }

    public function store(StorePartnerRequest $request)
    {
        $this->partnerService->create($request->validated());
        session()->flash('success', 'Data saved successfully.');
        return redirect()->route('partners.index')->with('success', 'Partner added successfully.');
    }

    public function update(UpdatePartnerRequest $request, $id)
    {
        $partner = Partner::findOrFail($id);
        $this->partnerService->update($partner, $request->validated());
        session()->flash('success', 'Data updated successfully.');
        return redirect()->route('partners.index')->with('success', 'Partner updated successfully.');
    }

    public function destroy($id)
    {
        $partner = Partner::findOrFail($id);
        $this->partnerService->delete($partner);
        return redirect()->back()->with('success', 'Partner deleted successfully.');
    }
}

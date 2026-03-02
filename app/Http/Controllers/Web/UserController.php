<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\TambahOperatorRequest;
use App\Http\Requests\Web\UpdateOperatorRequest;
use App\Models\User;
use App\Services\Web\OperatorService;

class UserController extends Controller
{
    public function __construct(
        protected OperatorService $operatorService,
    ) {}

    public function storeOperator(TambahOperatorRequest $request)
    {
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('operators', 'public');
        }

        $this->operatorService->createOperator($request->validated(), $photoPath);

        session()->flash('success', 'Data saved successfully.');
        return redirect()->route('admin.operators.index')->with('success', 'Operator added successfully.');
    }

    public function updateOperator(UpdateOperatorRequest $request, $id)
    {
        $user = User::findOrFail($id);
        $this->operatorService->updateOperator($user, $request->validated());

        session()->flash('success', 'Data updated successfully.');
        return redirect()->route('admin.operators.index')->with('success', 'Operator updated successfully.');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $this->operatorService->deleteUser($user);

        return redirect()->back()->with('success', 'Operator deleted successfully.');
    }
}

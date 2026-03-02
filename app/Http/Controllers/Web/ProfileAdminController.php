<?php

namespace App\Http\Controllers\Web;

use App\Http\Requests\Web\UpdateAdminProfileRequest;
use App\Http\Requests\Web\UpdateOperatorProfileRequest;
use App\Models\User;
use App\Services\Web\ProfileWebService;
use Illuminate\Http\Request;

class ProfileAdminController extends Controller
{
    public function __construct(
        protected ProfileWebService $profileWebService,
    ) {}

    public function showProfile(Request $request)
    {
        $email = session('user_email');
        $user = User::where('email', $email)->first();
        return view('admin.profile', compact('user'));
    }

    public function updateAdminProfile(UpdateAdminProfileRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        $this->profileWebService->updateProfile($user, $request->validated(), $photoPath);

        return redirect()->route('admin.profile.show')->with('success', 'Profile updated successfully.');
    }

    public function updateOperatorProfile(UpdateOperatorProfileRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('users', 'public');
        }

        $this->profileWebService->updateProfile($user, $request->validated(), $photoPath);

        return redirect()->route('operator.profile.show', $user->id)->with('success', 'Profile updated successfully.');
    }
}

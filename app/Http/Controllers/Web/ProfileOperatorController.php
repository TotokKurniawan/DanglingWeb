<?php

/**
 * @deprecated MVP
 *
 * Controller ini DITANGGUHKAN sementara di fase MVP.
 * Profil user internal kini dikelola oleh ProfileAdminController.
 *
 * Aktifkan kembali bersamaan dengan OperatorController ketika
 * role `operator` dihidupkan kembali.
 *
 * @see ProfileAdminController untuk implementasi aktif yang setara.
 */

namespace App\Http\Controllers\Web;

use App\Models\User;
use Illuminate\Http\Request;

class ProfileOperatorController extends Controller
{
    /** @deprecated Gunakan ProfileAdminController */
    public function showProfile(Request $request)
    {
        $email = session('user_email');
        $user = User::where('email', $email)->first();
        return view('operator.profile', compact('user'));
    }
}

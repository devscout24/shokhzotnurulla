<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class DealerSetupController extends Controller
{
    public function showSetupForm(Request $request, $token)
    {
        return view('auth.dealer-setup', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function setupAccount(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'User not found']);
        }

        if (! Password::broker()->tokenExists($user, $request->token)) {
            return back()->withErrors(['email' => 'Invalid or expired token']);
        }

        $user->forceFill([
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
        ])->save();

        if ($user->currentDealer) {
            $user->currentDealer->update([
                'status'    => \App\Enums\DealerStatus::ACTIVE,
                'is_active' => true,
            ]);
        }

        Password::broker()->deleteToken($user);

        // Auto-login the user after setup
        Auth::login($user);

        return redirect()->route('dealer.website.dashboard')->with('status', 'Your account has been verified and password has been set successfully. Welcome to your dashboard!');
    }
}

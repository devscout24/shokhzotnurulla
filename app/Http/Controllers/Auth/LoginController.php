<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use App\Traits\HandlesUserLogout;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    use AuthenticatesUsers, HandlesUserLogout;

    protected $redirectTo = '/dealer/website/dashboard';

    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect($this->redirectToDashboard(Auth::user()));
        }

        return view('auth.login');
    }

    // After Successful Login

    protected function authenticated(Request $request, $user): RedirectResponse
    {
        // Set permissions team
        setPermissionsTeamId($user->current_dealer_id);


        // Master validation — inactive, dealer, role etc
        if ($error = $user->validateUserLogin()) {
            AuditLogger::warning($request, 'Login blocked after auth', [
                'user_id' => $user->id,
                'email'   => $user->email,
                'reason'  => $error,
            ]);

            return $this->logoutWithMessage($request, $error);
        }

        // Update last login timestamp
        $user->updateQuietly(['last_login_at' => now()]);

        // Success log
        AuditLogger::info($request, 'User logged in successfully', [
            'user_id' => $user->id,
            'email'   => $user->email,
        ]);

        return redirect()->intended($this->redirectToDashboard($user));
    }

    // After Failed Login

    protected function sendFailedLoginResponse(Request $request)
    {
        AuditLogger::warning($request, 'Failed login attempt', [
            'email' => $request->input($this->username()),
        ]);

        throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }

    // After Logout

    protected function loggedOut(Request $request): RedirectResponse
    {
        AuditLogger::info($request, 'User logged out', [
            'ip' => $request->ip(),
        ]);

        return redirect()->route('login');
    }

    protected function redirectToDashboard($user)
    {
        return $user->isSystemUser()
            ? route('admin.dashboard')
            : route('dealer.website.dashboard');
    }
}
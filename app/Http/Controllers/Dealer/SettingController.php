<?php
namespace App\Http\Controllers\Dealer;

use App\Actions\User\UpdatePasswordAction;
use App\Actions\User\UpdateProfileAction;
use App\Actions\User\UpdateSecurityAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateSecurityRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly UpdateProfileAction $updateProfile,
        private readonly UpdatePasswordAction $updatePassword,
        private readonly UpdateSecurityAction $updateSecurity,
    ) {}

    public function profile(): View
    {
        return view('dealer.pages.settings.profile', [
            'user'      => Auth::user(),
            'timezones' => \DateTimeZone::listIdentifiers(\DateTimeZone::ALL),
        ]);
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $updated = ($this->updateProfile)(Auth::user(), $request->validated());

        if (! $updated) {
            return back()->with('info', 'No changes were made.');
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse | RedirectResponse
    {
        $user = Auth::user();

        if ($this->isPasswordReused($user, $request->input('password'))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'errors' => ['password' => ["You cannot reuse your last {$user->password_reuse_policy} passwords."]],
                ], 422);
            }
            return back()->withErrors([
                'password' => "You cannot reuse your last {$user->password_reuse_policy} passwords.",
            ]);
        }

        try {
            ($this->updatePassword)($user, $request->input('password'));
        } catch (Exception $e) {
            report($e);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Password could not be updated. Please try again.'], 500);
            }
            return back()->with('error', 'Password could not be updated. Please try again.');
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Password updated successfully.']);
        }

        return back()->with('success', 'Password updated successfully.');
    }

    public function authentication(): View
    {
        $user      = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $QR_Image = null;
        $secret   = null;

        if (! $user->google2fa_secret) {
            // Generate a new secret and store in session temporarily
            if (! session('google2fa_secret_setup')) {
                session(['google2fa_secret_setup' => $google2fa->generateSecretKey()]);
            }
            $secret = session('google2fa_secret_setup');

            // Generate the QR code image
            $qrCodeUrl = $google2fa->getQRCodeUrl(
                config('app.name'),
                $user->email,
                $secret
            );

            // Generate inline QR Code image using BaconQrCode
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
                new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
            );
            $writer   = new \BaconQrCode\Writer($renderer);
            $QR_Image = base64_encode($writer->writeString($qrCodeUrl));
        }

        return view('dealer.pages.settings.authentication', compact('user', 'QR_Image', 'secret'));
    }

    public function security(): View
    {
        return view('dealer.pages.settings.security', [
            'user' => Auth::user(),
        ]);
    }

    public function updateSecurity(UpdateSecurityRequest $request): RedirectResponse
    {
        $user = Auth::user();

        // Check if user is trying to enable 2FA without setting it up
        if ($request->validated('is_2fa_required') && ! $user->google2fa_secret) {
            return redirect()->route('dealer.settings.authentication')
                ->with('warning', 'Please set up your Google Authenticator before enabling 2FA.');
        }

        $updated = ($this->updateSecurity)($user, $request->validated());

        if (! $updated) {
            return back()->with('info', 'No changes were made.');
        }

        return back()->with('success', 'Account security updated successfully.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function isPasswordReused(User $user, string $newPassword): bool
    {
        $limit = $user->password_reuse_policy ?? 0;

        if ($limit === 0) {
            return false;
        }

        return $user->passwordHistories()
            ->latest()
            ->take($limit)
            ->pluck('password')
            ->contains(fn($old) => Hash::check($newPassword, $old));
    }
}

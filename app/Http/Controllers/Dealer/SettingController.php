<?php

namespace App\Http\Controllers\Dealer;

use Exception;
use App\Http\Controllers\Controller;
use App\Actions\User\UpdatePasswordAction;
use App\Actions\User\UpdateProfileAction;
use App\Actions\User\UpdateSecurityAction;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateSecurityRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly UpdateProfileAction  $updateProfile,
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

        if (!$updated) {
            return back()->with('info', 'No changes were made.');
        }

        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse|RedirectResponse
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
        return view('dealer.pages.settings.authentication');
    }

    public function security(): View
    {
        return view('dealer.pages.settings.security', [
            'user' => Auth::user(),
        ]);
    }

    public function updateSecurity(UpdateSecurityRequest $request): RedirectResponse
    {
        $updated = ($this->updateSecurity)(Auth::user(), $request->validated());

        if (!$updated) {
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
            ->contains(fn ($old) => Hash::check($newPassword, $old));
    }
}
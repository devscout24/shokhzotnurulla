<?php

namespace App\Actions\User;

use App\Models\User;
use App\Events\User\PasswordChanged;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdatePasswordAction
{
    public function __invoke(User $user, string $newPassword): void
    {
        DB::transaction(function () use ($user, $newPassword) {
            $user->passwordHistories()->create([
                'password' => $user->password,
            ]);
            $user->update([
                'password'                 => Hash::make($newPassword),
                'password_last_changed_at' => now(),
            ]);
        });

        Auth::logoutOtherDevices($newPassword);
        event(new PasswordChanged($user));
    }
}
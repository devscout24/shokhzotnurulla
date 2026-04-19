<?php

namespace App\Actions\User;

use App\Models\User;
use App\Events\User\SecuritySettingsUpdated;

class UpdateSecurityAction
{
    public function __invoke(User $user, array $data): bool
    {
        $user->fill($data);

        if (!$user->isDirty()) {
            return false;
        }

        $user->save();
        event(new SecuritySettingsUpdated($user));

        return true;
    }
}
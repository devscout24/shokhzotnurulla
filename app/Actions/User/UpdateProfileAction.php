<?php

namespace App\Actions\User;

use App\Models\User;
use App\Events\User\ProfileUpdated;

class UpdateProfileAction
{
    public function __invoke(User $user, array $data): bool
    {
        $user->fill($data);

        if (!$user->isDirty()) {
            return false;
        }

        $user->save();
        event(new ProfileUpdated($user));

        return true;
    }
}
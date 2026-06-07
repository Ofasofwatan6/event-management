<?php

namespace App\Observers;

use App\Models\User;
use App\Models\UserHistory;
use Illuminate\Support\Arr;

class UserHistoryObserver
{
    public function updated(User $user): void
    {
        if ($user->wasChanged(['name', 'email', 'phone', 'nickname', 'volunteer_status', 'city', 'profile_image'])) {
            $lastVersion = UserHistory::where('user_id', $user->id)->max('version') ?? 0;

            UserHistory::where('user_id', $user->id)
                ->whereNull('valid_to')
                ->update(['valid_to' => now()]);

            UserHistory::create([
                'user_id' => $user->id,
                'version' => $lastVersion + 1,
                'snapshot' => Arr::only($user->getOriginal(), [
                    'name', 'email', 'phone', 'nickname', 'volunteer_status', 'city', 'profile_image', 'role',
                ]),
                'valid_from' => now(),
            ]);
        }
    }
}

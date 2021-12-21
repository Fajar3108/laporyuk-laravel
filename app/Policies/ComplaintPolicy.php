<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function destroy(User $user, Complaint $complaint)
    {
        return $user->id === $complaint->user_id || $user->role->slug == 'admin' || $user->role->slug == 'petugas';
    }
}

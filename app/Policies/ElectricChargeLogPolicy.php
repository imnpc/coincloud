<?php

namespace App\Policies;

use App\Models\ElectricChargeLog;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ElectricChargeLogPolicy
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
    public function own(User $user, ElectricChargeLog $electricchargelog)
    {
        return $user->id === $electricchargelog->user_id;
    }
}

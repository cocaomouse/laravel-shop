<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\Models\UserAddress;

class UserAddressPolicy
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

    /**
     * 确定用户是否可以操作地址
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\UserAddress  $address
     * @return bool
     */
    public function own(User $user, UserAddress $address)
    {
        return $user->id == $address->user_id;
    }
}

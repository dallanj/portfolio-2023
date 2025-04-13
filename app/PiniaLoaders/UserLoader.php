<?php

namespace App\PiniaLoaders;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserLoader
{
    /**
     * Get all of the users
     * 
     * @return Object
     */
    public function all(): Object
    {
        return User::paginate();
    }

    /**
     * Get all of the users
     * 
     * @return Object
     */
    public function profile(): Object
    {
        $user = Auth::user();
        return $user ?: null;
    }
};

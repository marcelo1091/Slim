<?php

namespace App\Auth;

use App\Models\User;

class Auth
{
    public function attempt(string $nick, string $password): bool
    {
        if (!$user = User::whereNick($nick)->first()) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        return true;
    }
}

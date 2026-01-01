<?php

namespace App\Http\Services;

use App\Enums\RoleUserEnum;
use App\Exceptions\GeneralException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function loginWeb(array $attrs, string $role)
    {
        if (Auth::attempt([
            'email' => $attrs['email'],
            'password' => $attrs['password'],
            'role' => $role,
            'is_active' => true
        ])) {
            return Auth::user();
        }
        throw new GeneralException("Invalid credentials", 401);
    }

    public function loginApi(array $attrs, string $role)
    {
        $user = User::where([
            'email' => $attrs['email'],
            'role' => $role,
            'is_active' => true
        ])->first();

        if (! $user || ! Hash::check($attrs['password'], $user->password)) {
            throw new GeneralException("Invalid credentials", 401);
        }

        $token = $user->createToken('api_token')->accessToken;
        $user->access_token = $token;
        return $user;
    }

    

    public function profile()
    {
        return Auth::user();
    }
}

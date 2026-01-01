<?php

namespace App\Http\Services\Admin;

use App\Exceptions\GeneralException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{

    public function createUser($attrs)
    {
        $user = User::create([
            'role' => $attrs['role'],
            "name" => $attrs["name"],
            "email" => $attrs["email"],
            'password' => Hash::make($attrs['password']),
            'is_active' => $attrs['is_active'],
        ]);
        return $user;
    }

    public function getUserById(User $user): User
    {
        if (! $user) {
            throw new GeneralException("User not found", 404);
        }
        return $user;
    }

    public function updateUser($User, array $data): User
    {
        try {
            $User->update([
                "name" => $data["name"],
                "email" => $data["email"],
                'is_active' => $data['is_active']
            ]);
        } catch (\Exception $e) {
            throw new GeneralException("Failed to update User: " . $e->getMessage(), 500);
        }
        return $User;
    }


    public function deleteUser($User): bool
    {
        if ($User) {
            return $User->delete();
        }
        return throw new GeneralException("Failed to delete User");
    }

    public function UpdateStatusAccount($user, $data)
    {
        try {
            $user->update([
                'is_active' => $data['is_active']
            ]);
        } catch (\Exception $e) {
            throw new GeneralException("Failed to update User Account status: " . $e->getMessage(), 500);
        }
        return $user;
    }

    public function getUserList(array $params = [])
    {
        $query = User::query();

        if (isset($params['role'])) {
            $query->where('role', $params['role']);
        }

        if (isset($params['status'])) {
            $query->where('is_active', $params['status']);
        }

        if (isset($params['search'])) {
            $query->where('name', 'like', "%{$params['search']}%");
        }

        $orderBy = $params['order_by'] ?? 'created_at';
        $direction = $params['order_direction'] ?? 'desc';

      
        return $query->orderBy($orderBy, $direction)->paginate(10);
    }
}

<?php

namespace App\Http\Services\Admin;
use App\Models\User;

class DashboardService
{
    public function getDashboardStats()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        $latestUsers = User::orderBy('created_at', 'desc')->take(5)->get();


        return [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'latestUsers' => $latestUsers,

        ];
    }

}
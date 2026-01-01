<?php

namespace App\Http\Middleware;

use App\Enums\RoleUserEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('web')->user();

        if (! $user || $user->role !== RoleUserEnum::Admin) {
            return redirect()->route('admin.login')
                ->withErrors(['error' => 'You do not have permission to access this area.']);
        }

        return $next($request);
    }
}

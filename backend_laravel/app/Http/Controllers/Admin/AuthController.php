<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleUserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

   
    public function showLoginForm(): View
    {
        return view("admin.login");
    }

   
    public function login(LoginRequest $request): RedirectResponse
{
    $attrs = $request->validated();

    try {
        $user = $this->authService->loginWeb($attrs, RoleUserEnum::Admin);

        if (! $user->is_active) {
            Auth::logout();
            return redirect()->back()
                ->withErrors(['email' => 'Your account is disabled.'])
                ->withInput($request->only('email'));
        }

        Auth::guard('web')->login($user);

        return redirect()->intended(route("admin.home"));

    } catch (\App\Exceptions\GeneralException $e) {
        return redirect()->back()
            ->withErrors(['login' => 'Email or password is incorrect'])
            ->withInput($request->only('email'));
    }
}

    
  
    public function logout(): RedirectResponse
    {
        Auth::guard('web')->logout();
        return redirect()->route("admin.login");
    }
}

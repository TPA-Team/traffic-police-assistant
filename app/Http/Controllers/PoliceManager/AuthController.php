<?php

namespace App\Http\Controllers\PoliceManager;

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
        return view('policemanager.auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $attrs = $request->validated();

        $user = $this->authService->loginWeb($attrs, RoleUserEnum::Police_manager);

        Auth::guard('web')->login($user);

        return redirect()->intended(route('policemanager.home'));
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('web')->logout();
        return redirect()->route('policemanager.login');
    }
}

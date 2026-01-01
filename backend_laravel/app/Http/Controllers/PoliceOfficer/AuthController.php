<?php
    
 namespace App\Http\Controllers\PoliceOfficer;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Services\AuthService;
use App\Http\Resources\ProfileResource;
use App\Enums\RoleUserEnum;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(LoginRequest $request)
    {        

        $atrr = $request->validated();
        $user = $this->authService->loginApi($atrr, RoleUserEnum::Police_officer);
        return $this->success(new ProfileResource($user));
    }

    public function profile()
    {
        $profile = $this->authService->profile() ;
        return $this->success( new ProfileResource($profile));
    }
    
    public function logout(Request $request)
{
    $token = $request->user()->token(); 
    if ($token) {
        $token->revoke(); 
    }

    return response()->json([
        'status_code' => 200,
        'message'     => 'Logged out successfully',
        'data'        => [],
    ]);
}

}


   

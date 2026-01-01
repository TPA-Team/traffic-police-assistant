<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleUserEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreateUserRequest;
use App\Http\Requests\Admin\UpdateUserInfo;
use App\Http\Requests\Admin\UpdateUserStatus;
use App\Http\Requests\Admin\UserFilterRequest;
use App\Http\Services\Admin\UserService;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userServicee)
    {
        $this->userService = $userServicee;
    }

    public function index(UserFilterRequest $request)
    {

        $attr = $request->validated();
        $attr = $request->only(['status', 'search', 'order_by', 'order_direction']);
        $users = $this->userService->getUserList($attr);
        return view("admin.police.index", compact('users'));
    }

    public function create()
    {
        return view('admin.police.create');
    }

    public function store(CreateUserRequest $request)
    {
        $attr = $request->validated();
        $this->userService->createUser($attr);
        return redirect()->route("admin.users.index")->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user = $this->userService->getUserById($user);
        return view("admin.police.index", compact('user'));
    }



   public function edit(User $user)
{
    return view("admin.police.edit", compact('user'));
}

    public function saveupdate(UpdateUserInfo $request , User $user){
        $attr = $request->validated();
        $user = $this->userService->updateUser($user , $attr);
        return redirect()->route(route: "admin.users.index")->with("success", "User updated successfully");
    }

    public function destroy(User $user)
    {
        $user = $this->userService->deleteUser($user);
        return redirect()->route("admin.users.index")->with("success", "User deleted successfully");
    }

    public function toggleStatus( User $user)
{
    $user->is_active = !$user->is_active;
    $user->save();

    return redirect()->back()->with('success', 'User status updated successfully.');
}


    
}

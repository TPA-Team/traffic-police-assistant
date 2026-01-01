<?php

namespace App\Http\Requests\Admin;
use App\Enums\RoleUserEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->role == RoleUserEnum::Admin ;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                'name' => ['string' , 'required'],
                'email' => ["email" , "string" ,"required" , "unique:users,email"],
                'password' => ["required" , "string" , "min:6"],
                'role' => ["string" , "required" , Rule::in([ RoleUserEnum::Police_manager,
                RoleUserEnum::Police_officer ]) ],
                'is_active' => ['nullable' ],
                "phone" => ["string" ,"nullable" , 'digits:10'],
            
        ];
    }
}

<?php

namespace App\Http\Requests\Admin;

use App\Enums\RoleUserEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user && $user->role == RoleUserEnum::Admin;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => 'nullable|in:active,inactive',
            'search' => 'nullable|string|max:255',
            'order_by' => 'nullable|in:name,email,created_at',
            'order_direction' => 'nullable|in:asc,desc',
        ];
    }
}

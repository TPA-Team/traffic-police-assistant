<?php

namespace App\Http\Requests\PoliceOfficer;

use App\Enums\RoleUserEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateViolationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = Auth::user();
        return $user->role == RoleUserEnum::Police_officer;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Vehicle info
            'vehicle_plate' => ['required', 'string', 'max:50'],
            'vehicle_color' => ['nullable', 'string', 'max:50'],
            'vehicle_model' => ['nullable', 'string', 'max:100'],
            'vehicle_owner' => ['nullable', 'string', 'max:255'],

            // Location info
            'street_name'   => ['required', 'string', 'max:255'],
            'city_id' => ['required', 'exists:cities,id'],
            'landmark'     => ['nullable', 'string', 'max:255'],

            // Violation info
            'violation_type_id' => ['required', 'exists:violation_types,id'],
            'description'      => ['nullable', 'string', 'max:2000'],
            'vehicle_snapshot' => ['nullable', 'array'],
            'occurred_at'      => ['required', 'date'],
        ];
    }
}

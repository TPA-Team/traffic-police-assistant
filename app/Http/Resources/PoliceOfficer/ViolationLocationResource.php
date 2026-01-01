<?php

namespace App\Http\Resources\PoliceOfficer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationLocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'city'        => [
                'id'   => $this->city->id,
                'name' => $this->city->name,
            ],
            'street_name' => $this->street_name,
            'landmark'    => $this->landmark,
        ];
    }
}

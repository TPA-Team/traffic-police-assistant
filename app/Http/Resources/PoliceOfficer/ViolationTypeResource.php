<?php

namespace App\Http\Resources\PoliceOfficer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'name' => $this->name,
            'description' => $this->description ,
            'fine_amount' => $this->fine_amount ,
        ];
    }
}

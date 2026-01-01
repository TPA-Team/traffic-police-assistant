<?php

namespace App\Http\Resources\PoliceOfficer;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return  [
            'id' => $this->id ,
            'plate_number' => $this->plate_number,
            'owner_name' => $this->owner_name ,
            'model' => $this->model ,
            'color' => $this->color,
        ];
    }
}

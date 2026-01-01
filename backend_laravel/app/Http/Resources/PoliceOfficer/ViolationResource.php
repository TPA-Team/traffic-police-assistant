<?php

namespace App\Http\Resources\PoliceOfficer;

use App\Http\Resources\ProfileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationResource extends JsonResource
{
    public function toArray(Request $request): array
{
    return [
        'id'             => $this->id,
        'vehicle'        => new VehicleResource($this->whenLoaded('vehicle')),
        'violation_type' => new ViolationTypeResource($this->whenLoaded('violationType')),
        'location'       => new ViolationLocationResource($this->whenLoaded('violationLocation')),
        'description'    => $this->description,
        'fine_amount'    => (float) $this->fine_amount,
        'vehicle_snapshot' => json_decode($this->vehicle_snapshot, true),
        'appeal'         => $this->appeal ? [
            'id'          => $this->appeal->id,
            'status'      => $this->appeal->status,
            'reason'      => $this->appeal->reason,
            'decision_note' => $this->appeal->decision_note,
            'created_at'  => $this->appeal->created_at,
            'updated_at'  => $this->appeal->updated_at,
        ] : null,
        'occurred_at'    => optional($this->occurred_at)->toDateTimeString(),
        'created_at'     => $this->created_at->toDateTimeString(),
        'updated_at'     => $this->updated_at->toDateTimeString(),
    ];
}


    public function with(Request $request): array
    {
        return [
            'meta' => [
                'resource' => 'violation',
            ],
        ];
    }
}

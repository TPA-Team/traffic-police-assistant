<?php

namespace App\Http\Services\PoliceOfficer;

use App\Models\Vehicle;
use App\Models\Violation;
use App\Models\ViolationLocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViolationService
{


    public function getViolationList()
    {
        $violations = Violation::where('reported_by', Auth::id())
            ->with(['violationLocation.city', 'violationType', 'vehicle'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $violations;
    }

    public function createViolation(array $data)
    {
        return DB::transaction(function () use ($data) {
            $vehicle = Vehicle::firstOrCreate(
                ['plate_number' => $data['vehicle_plate']],
                [
                    'color'      => $data['vehicle_color'] ?? null,
                    'model'      => $data['vehicle_model'] ?? null,
                    'brand'      => $data['vehicle_brand'] ?? null,
                    'owner_name' => $data['vehicle_owner'] ?? null,
                ]
            );

            $vehicleSnapshot = [
                'plate_number' => $vehicle->plate_number,
                'owner_name'   => $vehicle->owner_name,
            ];

            $violationType = \App\Models\ViolationType::findOrFail($data['violation_type_id']);


            $location = ViolationLocation::create([
                'city_id'     => $data['city_id'],
                'street_name' => $data['street_name'],
                'landmark'    => $data['landmark'] ?? null,
            ]);

            $violation = Violation::create([
                'vehicle_id'            => $vehicle->id,
                'violation_type_id'     => $data['violation_type_id'],
                'violation_location_id' => $location->id,
                'reported_by'           => Auth::id(),
                'description'           => $data['description'] ?? null,
                'fine_amount'           => $violationType->fine_amount,
                'vehicle_snapshot' => json_encode([
                    'plate_number' => $vehicle->plate_number,
                    'owner_name'   => $data['vehicle_owner'] ?? null,
                ]),
                'occurred_at'           => $data['occurred_at'],
            ]);

            return $violation;
        },);
    }
}

<?php
namespace App\Http\Services\Citizen;

use App\Models\Appeal;
use App\Models\Violation;

class ViolationService
{
    public function fetchViolations(array $data = [])
    {
        $query = Violation::with(['violationLocation.city', 'violationType', 'vehicle' ,  'appeal']);

        if (isset($data['plate'])) {
            $query->where('vehicle_snapshot->plate_number', $data['plate']);
        }

        return $query->orderBy('created_at', 'desc')
                     ->paginate(5);
    }

    public function createAppeal(array $data, int $violationId)
    {
        $appeal = Appeal::create([
            'violation_id' => $violationId ,  
            'status'        => $data['status'] ?? 'pending',
            'reason'        => $data['reason'],
            'decision_note' => $data['decision_note'] ?? null,
        ]);

       

        return $appeal;
    }
}

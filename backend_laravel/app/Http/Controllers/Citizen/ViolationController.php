<?php

namespace App\Http\Controllers\Citizen;

use App\Http\Controllers\Controller;
use App\Http\Services\Citizen\ViolationService;
use App\Models\Violation;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    protected ViolationService $violationService;

    public function __construct(ViolationService $violationService)
    {
        $this->violationService = $violationService;
    }

    public function page()
    {
        return view('citizen.index');
    }

    public function search(Request $request)
    {
        $plate = $request->query('plate');
        if (!$plate) {
            return response()->json([
                'message' => 'Plate number is required'
            ], 400);
        }

        $violations = $this->violationService->fetchViolations(['plate' => $plate]);
        return response()->json($violations);
    }

   public function store(Request $request) {
    $validated = $request->validate([
        'violation_id' => 'required|integer|exists:violations,id',
        'reason'       => 'required|string',
    ]);

    $violation = Violation::findOrFail($validated['violation_id']);

    if ($violation->appeal) {
        return response()->json([
            'success' => false,
            'message' => 'An appeal for this violation already exists.',
            'appeal'  => $violation->appeal
        ], 409); // conflict
    }

    $appeal = $this->violationService->createAppeal([
        'reason' => $validated['reason']
    ], $validated['violation_id']);

    return response()->json([
        'success'   => true,
        'appeal_id' => $appeal->id,
    ], 201);
}

}

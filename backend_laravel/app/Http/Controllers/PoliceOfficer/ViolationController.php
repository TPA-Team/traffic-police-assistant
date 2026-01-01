<?php 

namespace App\Http\Controllers\PoliceOfficer;

use App\Http\Controllers\Controller ;

use App\Http\Requests\PoliceOfficer\CreateViolationRequest;
use App\Http\Resources\PoliceOfficer\ViolationResource;
use App\Http\Services\PoliceOfficer\ViolationService;

class ViolationController extends Controller
{

    protected $violationService ;

    public function __construct(ViolationService $violationService){
        $this->violationService = $violationService;
    }
    public function index(){
        $violations =  $this->violationService->getViolationList();
      return $this->success(
       ViolationResource::collection($violations) );
    }

    public function create(CreateViolationRequest $request){
        $atrr =  $request->validated();
        $violation = $this->violationService->createViolation($atrr);
        return $this->success(new ViolationResource($violation)) ;

    }
}
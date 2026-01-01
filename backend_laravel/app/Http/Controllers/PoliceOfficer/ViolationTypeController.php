<?php

namespace App\Http\Controllers\PoliceOfficer;

use App\Http\Controllers\Controller;
use App\Http\Services\Admin\ViolationTypeService;
use Illuminate\Http\Request;

class ViolationTypeController extends Controller
{
    protected $violationType ;

    public function __construct( ViolationTypeService $violationType)
    {
        $this->violationType = $violationType;
    }

    public function index(){
         $violationTypes = $this->violationType->getViolationTypeList();
      return $violationTypes ;
    }
}

<?php

namespace App\Http\Controllers\PoliceOfficer;

use App\Http\Controllers\Controller;
use App\Http\Services\PoliceOfficer\CityService;
use Illuminate\Http\Request;

class CityController extends Controller
{
    protected $cityservice ;

    public function __construct(CityService $cityservice)
    {
        $this->cityservice = $cityservice;
    }

    public function index(){
         $cities = $this->cityservice->getcitylist();
        return $cities ;
    }
}

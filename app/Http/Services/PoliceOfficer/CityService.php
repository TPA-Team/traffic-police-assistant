<?php 

namespace App\Http\Services\PoliceOfficer;

use App\Models\City;

class CityService{

    public function getcitylist(){
        return City::all();
    }
}
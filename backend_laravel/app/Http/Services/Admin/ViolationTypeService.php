<?php 

namespace App\Http\Services\Admin;

use App\Models\ViolationType;

class ViolationTypeService{


    public function createViolationType(  array $atrr ){
        $violation_type = ViolationType::create(
            [ "name" => $atrr['name'],
            'description' => $atrr['description'],
            "fine_amount" => $atrr['fine_amount']]
        );

        return $violation_type;
    }

    public function getViolationTypeList(){
         $violationType = ViolationType::all();

         return $violationType;
    }

}
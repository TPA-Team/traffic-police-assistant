<?php 

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

abstract class Controller{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

public function success($data , $massage= "Success" , $code = 200) {
return response()->json([
    "status_code" => $code , 
    "massage" => $massage ,
    "data" => $data ,
] , $code);
}}


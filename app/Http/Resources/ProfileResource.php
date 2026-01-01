<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = [
            "id" => $this->id , 
            "name" => $this->name , 
            "email" => $this->email ,
            "role" => $this->role , 
            "is_active" => $this->is_active,
            "profile_image" => $this->profile_image,
            "created_at" => $this->created_at , 
            "updated_at" => $this->updated_at ,
        ];

        if($this->access_token){
            $user["token"] = $this->access_token;
        }
        return $user ;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Vehicle extends Model
{
        use  HasFactory, Notifiable , HasApiTokens  ;

        protected $fillable = [ 'plate_number' , 'owner_name' , 'model' , 'color'];


public function violations()
{
    return $this->hasMany(Violation::class);
}


}

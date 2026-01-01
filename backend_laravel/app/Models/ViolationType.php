<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViolationType extends Model
{
    protected $fillable = ['name' , 'description' , 'fine_amount'];


    public function violations(){
        return $this->hasMany(Violation::class);
    }
}

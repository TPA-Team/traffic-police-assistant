<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Violation extends Model
{
            use  HasFactory, Notifiable , HasApiTokens  ;

    protected $fillable = [
    'vehicle_id',
    'violation_type_id',
    'violation_location_id',
    'reported_by',
    'description',
    'fine_amount',
    'vehicle_snapshot',
    'occurred_at',
];


public function vehicle()
{
    return $this->belongsTo(Vehicle::class);
}

public function violationLocation()
{
    return $this->belongsTo(ViolationLocation::class);
}

public function violationType()
{
    return $this->belongsTo(ViolationType::class);
}


public function reporter()
{
    return $this->belongsTo(User::class, 'reported_by');
}

public function appeal()
{
    return $this->hasOne(Appeal::class);
}





}

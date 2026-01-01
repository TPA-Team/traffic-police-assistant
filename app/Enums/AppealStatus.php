<?php

namespace App\Enums;

enum AppealStatus
{
    const Pending = 'pending';
    const  Rejected= 'rejected';
    const Completed= 'completed';
    const Approved = 'approved' ;
}

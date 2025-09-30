<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentPolicy extends Model
{
    protected $fillable = ['company_id','name','dp_percent','balance_days_before_start','auto_cancel_if_dp_overdue','grace_days'];
}


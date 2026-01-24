<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class PaymentPolicy extends Model
{
    use Auditable;

    protected $fillable = ['company_id','name','dp_percent','balance_days_before_start','auto_cancel_if_dp_overdue','grace_days'];
}


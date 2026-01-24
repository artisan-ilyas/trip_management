<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CancellationPolicyRule extends Model
{
    use Auditable;

    protected $fillable = ['cancellation_policy_id','days_from','days_to','penalty_percent','refundable','company_id'];
}


<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RatePlanRule extends Model
{
    use Auditable;

    protected $fillable = ['rate_plan_id','room_id','base_price','extra_bed_price'.'company_id'];

    public function ratePlan() {
        return $this->belongsTo(RatePlan::class);
    }
}

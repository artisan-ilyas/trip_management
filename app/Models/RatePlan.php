<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class RatePlan extends Model
{
    use Auditable;

    protected $fillable = ['company_id','boat_id','name','currency','base_price_type','tax_included'];

    public function rules() { return $this->hasMany(RatePlanRule::class); }
}


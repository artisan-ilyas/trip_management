<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    use Auditable;

    protected $fillable = ['company_id','name'];

    public function rules() {
        return $this->hasMany(CancellationPolicyRule::class);
    }
}


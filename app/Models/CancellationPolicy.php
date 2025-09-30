<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CancellationPolicy extends Model
{
    protected $fillable = ['company_id','name'];

    public function rules() {
        return $this->hasMany(CancellationPolicyRule::class);
    }
}


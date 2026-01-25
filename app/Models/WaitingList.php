<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class WaitingList extends Model
{
    use Auditable;

    protected $fillable = [
        'company_id','availability_id','party_size','name','email','phone',
        'notes','source','status'
    ];

    public function availability()
    {
        return $this->belongsTo(Trip::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}



<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherGuest extends Model
{
    protected $fillable = [
        'guest_id', 'name', 'gender', 'email', 'password', 'dob', 'passport',
        'nationality', 'cabin', 'surfLevel', 'boardDetails','company_id'
    ];

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
}


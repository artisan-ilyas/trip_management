<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    protected $fillable = [
        'title',
        'region',
        'status',
        'leading_guest_id',
        'notes',
        'trip_type',
        'start_date',
        'end_date',
        'guests',
        'price',
        'boat',
        'agent_id',
        'guest_form_token',
        'guest_form_url',
        'company_id'
    ];

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

       public function leadingGuest()
    {
        return $this->belongsTo(Guest::class, 'leading_guest_id');
    }
    // Trip.php
public function guestList()
{
    return $this->hasMany(Guest::class);
}

// Guest.php
public function otherGuests()
{
    return $this->hasMany(OtherGuest::class);
}

public function agents()
{
    return $this->belongsToMany(Agent::class);
}

//booking
public function bookings()
{
    return $this->hasMany(Booking::class);
}


}


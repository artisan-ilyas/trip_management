<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
protected $fillable = [
    'trip_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'pickup_location',
    'dropoff_location',
    'booking_date',
    'number_of_guests',
    'total_price',
    'status',
    'payment_status',
    'payment_method',
    'assigned_agent_id',
    'source',
    'notes',
    'comments',
    'guest_form_token',
    'guest_form_url',
];


    // public function agent()
    // {
    //     return $this->belongsTo(Agent::class);
    // }

       public function leadingGuest()
    {
        return $this->belongsTo(Guest::class, 'leading_guest_id');
    }
    
    // Trip.php
public function trip()
{
    return $this->belongsTo(Trip::class);
}






}


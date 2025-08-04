<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'name', 'gender', 'trip_id', 'email', 'dob', 'passport', 'nationality', 'cabin', 'surfLevel', 'boardDetails',
        'arrivalFlightDate', 'arrivalFlightNumber', 'arrivalAirport', 'arrivalTime', 'hotelPickup',
        'departureFlightDate', 'departureFlightNumber', 'departureAirport', 'departureTime',
        'medicalDietary', 'specialRequests', 'insuranceName', 'policyNumber',
        'emergencyName', 'emergencyRelation', 'emergencyPhone',
        'guestWhatsapp', 'guestEmail',
        'image_path', 'pdf_path', 'video_path',
    ];

    public function otherGuests()
    {
        return $this->hasMany(OtherGuest::class);
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingInstallment extends Model
{
    protected $fillable = ['booking_id','amount','due_date','paid'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

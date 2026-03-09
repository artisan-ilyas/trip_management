<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = ['booking_id','amount','paid_at','payment_method','invoice_number'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}

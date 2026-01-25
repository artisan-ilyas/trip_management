<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    use Auditable;

    protected $fillable = ['name'];

    public function departureSlots()
    {
        return $this->hasMany(Slot::class, 'departure_port_id');
    }

    public function arrivalSlots()
    {
        return $this->hasMany(Slot::class, 'arrival_port_id');
    }
}

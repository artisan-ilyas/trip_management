<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    use Auditable;

    protected $fillable = ['name'];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }
}

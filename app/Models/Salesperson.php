<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Salesperson extends Model
{
    use Auditable;

    protected $fillable = ['name','email','phone'];
}


<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use Auditable;
    use HasFactory;

    protected $fillable = ['name', 'symbol', 'code','rate'];
}

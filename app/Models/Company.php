<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use Auditable;

    protected $fillable = [
    'name',
    'legal_name',
    'slug',
    'currency',
    'timezone',
    'billing_email',
    'address',
    'vat_tax_id',
];

}

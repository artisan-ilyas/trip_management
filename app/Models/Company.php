<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
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

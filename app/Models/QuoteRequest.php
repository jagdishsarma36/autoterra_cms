<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuoteRequest extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'country', 'product',
        'term', 'seats', 'message', 'status', 'notes',
    ];
}

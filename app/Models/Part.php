<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Part extends Model
{
    protected $fillable = ['name', 'category', 'description', 'image', 'compatibility'];

    protected $casts = [
        'compatibility' => 'array',
    ];
}


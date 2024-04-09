<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use HasUuids;

    protected $fillable = [
        'log',
    ];

    protected $casts = [
        'log' => 'array',
    ];
}

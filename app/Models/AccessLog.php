<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AccessLog extends Model
{
    protected $primaryKey = ['id', 'created_at'];

    public $incrementing = false;

    protected $fillable = [
        'log',
    ];

    protected $casts = [
        'log' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Model $model) {
            $model->id = (string) Str::orderedUuid();
        });
    }
}

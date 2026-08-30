<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'ip_address',
        'user_agent',
        'referer',
        'device_type',
        'duration_seconds',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'duration_seconds' => 'integer',
    ];
}
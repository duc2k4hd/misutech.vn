<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'zalo_phone',
        'department',
        'department_type',
        'avatar',
        'note',
        'sort_order',
        'is_active',
        'show_in_popup',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'show_in_popup' => 'boolean',
        'sort_order'    => 'integer',
    ];
}

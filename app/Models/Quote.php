<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_company',
        'customer_tax_code',
        'customer_address',
        'subtotal',
        'discount_percent',
        'vat_percent',
        'total_amount',
        'items_count',
        'notes',
        'ip_address',
        'user_agent',
        'referer',
        'device_type',
        'duration_seconds',
        'action_type',
        'status',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
        'duration_seconds' => 'integer',
        'subtotal' => 'float',
        'total_amount' => 'float',
    ];

    public function items()
    {
        return $this->hasMany(QuoteItem::class);
    }
}

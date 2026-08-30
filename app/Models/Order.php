<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'order_code', 'user_id', 'customer_name', 'customer_phone', 'customer_email',
        'shipping_address', 'order_notes', 'subtotal', 'discount_amount', 'shipping_fee',
        'total_amount', 'status', 'payment_status', 'payment_method'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function items() { return $this->hasMany(OrderItem::class); }
}
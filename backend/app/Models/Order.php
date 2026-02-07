<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'total_items',
        'subtotal',
        'discount_amount',
        'coupon_code',
        'shipping_method_id',
        'shipping_cost',
        'total',
        'status',
        'tracking_number',
        'tracking_url',
        'shipped_at',
        'payment_method',
        'payment_status',
        'payment_transaction_id',
        'payment_metadata',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'note',
    ];

    protected $casts = [
        'shipped_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function returns()
    {
        return $this->hasMany(OrderReturn::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function notes()
    {
        return $this->hasMany(OrderNote::class)->orderBy('created_at', 'desc');
    }

    public function internalNotes()
    {
        return $this->hasMany(OrderNote::class)->where('is_internal', true)->orderBy('created_at', 'desc');
    }

    public function publicNotes()
    {
        return $this->hasMany(OrderNote::class)->where('is_internal', false)->orderBy('created_at', 'desc');
    }
}

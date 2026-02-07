<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'return_number',
        'status',
        'reason',
        'description',
        'refund_amount',
        'refund_method',
        'refund_status',
        'admin_notes',
        'requested_at',
        'approved_at',
        'completed_at',
    ];

    protected $casts = [
        'refund_amount' => 'decimal:2',
        'requested_at' => 'datetime',
        'approved_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($return) {
            if (!$return->return_number) {
                $return->return_number = 'RET-' . strtoupper(Str::random(10));
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderReturnItem::class);
    }

    /**
     * Check if return can be approved
     */
    public function canBeApproved()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if return can be cancelled
     */
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'approved']);
    }
}

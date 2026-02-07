<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NewsletterSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'status',
        'token',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            if (!$subscription->token) {
                $subscription->token = Str::random(32);
            }
        });
    }

    public function unsubscribe()
    {
        $this->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(VariantAttributeValue::class)->orderBy('sort_order');
    }

    public function activeValues()
    {
        return $this->hasMany(VariantAttributeValue::class)->orderBy('sort_order');
    }
}

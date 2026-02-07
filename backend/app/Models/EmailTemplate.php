<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'type',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (!$template->slug) {
                $template->slug = Str::slug($template->name);
            }
        });
    }

    /**
     * Replace variables in template
     */
    public function render(array $data)
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($data as $key => $value) {
            $subject = str_replace('{{' . $key . '}}', $value, $subject);
            $body = str_replace('{{' . $key . '}}', $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'status',
        'posted_at',
        'discarded_at',
    ];

    protected $casts = [
        'posted_at' => 'datetime',
        'discarded_at' => 'datetime',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}


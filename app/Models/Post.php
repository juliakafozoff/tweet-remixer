<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'source_url',
        'archived_at',
    ];

    protected $casts = [
        'archived_at' => 'datetime',
    ];

    /**
     * Scope the query to only include active (non-archived) posts.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('archived_at');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tweets(): HasMany
    {
        return $this->hasMany(Tweet::class);
    }

    public function pendingTweets(): HasMany
    {
        return $this->tweets()->where('status', 'pending');
    }
}


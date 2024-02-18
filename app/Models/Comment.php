<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Comment extends Model
{
    use HasFactory;

    public $timestamps = [
        'created_at',
    ];

    public const UPDATED_AT = null;

    protected $fillable = [
        'parent_id',
        'post_id',
        'user_id',
        'body',
    ];

    protected $with = [
        'user',
        'recursiveComments',
    ];

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id');
    }

    /**
     * @return HasMany
     */
    public function recursiveComments(): HasMany
    {
        return $this->comments();
    }
}

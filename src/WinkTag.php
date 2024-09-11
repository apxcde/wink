<?php

namespace Wink;

use Glhd\Bits\Database\HasSnowflakes;
use Glhd\Bits\Snowflake;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WinkTag extends AbstractWinkModel
{
    use HasSnowflakes;

    protected $guarded = [];

    protected $table = 'wink_tags';

    protected $casts = [
        'id' => 'string',
        'meta' => 'array',
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(WinkPost::class, 'wink_posts_tags', 'tag_id', 'post_id');
    }

    protected static function boot(): void
    {
        parent::boot();

        static::deleting(function ($item) {
            $item->posts()->detach();
        });
    }
}

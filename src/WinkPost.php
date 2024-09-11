<?php

namespace Wink;

use DateTimeInterface;
use Glhd\Bits\Snowflake;
use Illuminate\Support\HtmlString;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WinkPost extends AbstractWinkModel
{
    use HasSnowflakes;

    protected $guarded = [];

    protected $table = 'wink_posts';

    protected $casts = [
        'id' => Snowflake::class,
        'meta' => 'array',
        'published' => 'boolean',
        'markdown' => 'boolean',
        'publish_date' => 'datetime',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WinkTag::class, 'wink_posts_tags', 'post_id', 'tag_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(WinkAuthor::class, 'author_id');
    }

    public function getContentAttribute(): HtmlString|string
    {
        if (! $this->markdown) {
            return $this->body;
        }

        $converter = new GithubFlavoredMarkdownConverter([
            'allow_unsafe_links' => false,
        ]);

        return new HtmlString($converter->convertToHtml($this->body));
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('published', false);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->published()->where('publish_date', '<=', now());
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('publish_date', '>', now());
    }

    public function scopeBeforePublishDate(Builder $query, string $date): Builder
    {
        return $query->where('publish_date', '<=', $date);
    }

    public function scopeAfterPublishDate(Builder $query, string $date): Builder
    {
        return $query->where('publish_date', '>', $date);
    }

    public function scopeTag(Builder $query, string $slug): Builder
    {
        return $query->whereHas('tags', function ($query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s');
    }
}

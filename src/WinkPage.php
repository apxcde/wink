<?php

namespace Wink;

use Glhd\Bits\Database\HasSnowflakes;
use Glhd\Bits\Snowflake;

class WinkPage extends AbstractWinkModel
{
    use HasSnowflakes;

    protected $guarded = [];

    protected $table = 'wink_pages';

    protected $casts = [
        'id' => 'string',
        'body' => 'string',
        'meta' => 'array',
    ];

    public function getContentAttribute(): string
    {
        return $this->body;
    }
}

<?php

namespace Wink;

use Glhd\Bits\Snowflake;
use Glhd\Bits\Database\HasSnowflakes;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WinkAuthor extends AbstractWinkModel implements Authenticatable
{
    use HasSnowflakes;

    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected $table = 'wink_authors';

    protected $rememberTokenName = 'remember_token';

    protected $casts = [
        'id' => Snowflake::class,
        'meta' => 'array',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(WinkPost::class, 'author_id');
    }

    public function getAuthIdentifierName(): string
    {
        return $this->getKeyName();
    }

    public function getAuthIdentifier(): mixed
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    public function getAuthPassword(): string
    {
        return $this->password;
    }

    public function getAuthPasswordName(): string
    {
        return 'password';
    }

    public function getRememberToken(): ?string
    {
        if (! empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    public function setRememberToken($value): void
    {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    public function getRememberTokenName(): string
    {
        return $this->rememberTokenName;
    }

    public function getAvatarAttribute($value): string
    {
        return $value ?: 'https://secure.gravatar.com/avatar/'.md5(strtolower(trim($this->email))).'?s=80';
    }
}

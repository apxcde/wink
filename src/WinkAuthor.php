<?php

namespace Wink;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property string $id
 * @property string $slug
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $bio
 * @property string $avatar
 * @property string|null $remember_token
 * @property CarbonInterface $updated_at
 * @property CarbonInterface $created_at
 * @property array<mixed>|null $meta
 * @property-read Collection<WinkPost> $posts
 */
class WinkAuthor extends AbstractWinkModel implements Authenticatable
{
    protected $guarded = [];

    protected $hidden = ['password', 'remember_token'];

    protected $table = 'wink_authors';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $rememberTokenName = 'remember_token';

    protected $casts = [
        'meta' => 'array',
    ];

    public function posts(): \Illuminate\Database\Eloquent\Relations\HasMany
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

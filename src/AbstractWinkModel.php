<?php

namespace Wink;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractWinkModel extends Model
{
    public function getConnectionName(): string
    {
        return config('wink.database_connection');
    }
}

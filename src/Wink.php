<?php

namespace Wink;

class Wink
{
    public static function scriptVariables(): array
    {
        return [
            'unsplash_key' => config('services.unsplash.key'),
            'path' => config('wink.path'),
            'preview_path' => config('wink.preview_path'),
            'author' => auth('wink')->check() ? auth('wink')->user()->only('name', 'avatar', 'id') : null,
            'default_editor' => config('wink.editor.default'),
        ];
    }
}

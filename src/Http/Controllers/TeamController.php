<?php

namespace Wink\Http\Controllers;

use Wink\WinkAuthor;
use Glhd\Bits\Snowflake;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Wink\Http\Resources\TeamResource;

class TeamController
{
    public function index()
    {
        $entries = WinkAuthor::when(request()->has('search'), function ($q) {
            $q->where('name', 'LIKE', '%'.request('search').'%');
        })
            ->orderBy('created_at', 'DESC')
            ->withCount('posts')
            ->paginate(config('wink.pagination.teams', 30));

        return TeamResource::collection($entries);
    }

    public function show($id = null)
    {
        if ($id === 'new') {
            return response()->json([
                'entry' => WinkAuthor::make([
                    'id' => Snowflake::make()->id(),
                ]),
            ]);
        }

        $entry = WinkAuthor::findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    public function store($id)
    {
        $data = [
            'name' => request('name'),
            'slug' => request('slug'),
            'email' => request('email'),
            'bio' => request('bio'),
            'avatar' => request('avatar'),
            'meta' => request('meta', (object) []),
        ];

        validator($data, [
            'meta.theme' => 'in:dark,light',
            'name' => 'required',
            'slug' => 'required|'.Rule::unique(config('wink.database_connection').'.wink_authors', 'slug')->ignore(request('id')),
            'email' => 'required|email|'.Rule::unique(config('wink.database_connection').'.wink_authors', 'email')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? WinkAuthor::findOrFail($id) : new WinkAuthor(['id' => request('id')]);

        if (request('password')) {
            $entry->password = Hash::make(request('password'));
        }

        if (request('email') !== $entry->email && Str::contains($entry->avatar, 'gravatar')) {
            unset($data['avatar']);

            $entry->avatar = null;
        }

        $entry->fill($data);

        $entry->save();

        return response()->json([
            'entry' => $entry->fresh(),
        ]);
    }

    public function delete($id)
    {
        $entry = WinkAuthor::findOrFail($id);

        if ($entry->posts()->count()) {
            return response()->json(['message' => 'Please remove the author\'s posts first.'], 402);
        }

        if ($entry->id == auth('wink')->user()->id) {
            return response()->json(['message' => 'You cannot delete yourself.'], 402);
        }

        $entry->delete();
    }
}

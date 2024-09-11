<?php

namespace Wink\Http\Controllers;

use Wink\WinkTag;
use Glhd\Bits\Snowflake;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Wink\Http\Resources\TagsResource;

class TagsController
{
    public function index()
    {
        $entries = WinkTag::when(request()->has('search'), function ($q) {
            $q->where('name', 'LIKE', '%'.request('search').'%');
        })
            ->orderBy('created_at', 'DESC')
            ->withCount('posts')
            ->paginate(config('wink.pagination.tags', 30));

        return TagsResource::collection($entries);
    }

    public function show($id = null)
    {
        if ($id === 'new') {
            return response()->json([
                'entry' => WinkTag::make([
                    'id' => Snowflake::make()->id(),
                ]),
            ]);
        }

        $entry = WinkTag::findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    /**
     * Store a single category.
     *
     * @param  string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function store($id)
    {
        $data = [
            'name' => request('name'),
            'slug' => request('slug'),
            'meta' => request('meta', (object) []),
        ];

        validator($data, [
            'name' => 'required',
            'slug' => 'required|'.Rule::unique(config('wink.database_connection').'.wink_tags', 'slug')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? WinkTag::findOrFail($id) : new WinkTag(['id' => request('id')]);

        $entry->fill($data);

        $entry->save();

        return response()->json([
            'entry' => $entry->fresh(),
        ]);
    }

    /**
     * Return a single tag.
     *
     * @param  string  $id
     * @return void
     */
    public function delete($id)
    {
        $entry = WinkTag::findOrFail($id);

        $entry->delete();
    }
}

<?php

namespace Wink\Http\Controllers;

use Wink\WinkPage;
use Glhd\Bits\Snowflake;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Wink\Http\Resources\PagesResource;

class PagesController
{
    public function index()
    {
        $entries = WinkPage::when(request()->has('search'), function ($q) {
            $q->where('title', 'LIKE', '%'.request('search').'%');
        })
            ->orderBy('created_at', 'DESC')
            ->paginate(config('wink.pagination.pages', 30));

        return PagesResource::collection($entries);
    }

    public function show($id = null)
    {
        if ($id === 'new') {
            return response()->json([
                'entry' => WinkPage::make(['id' => Snowflake::make()->id()]),
            ]);
        }

        $entry = WinkPage::findOrFail($id);

        return response()->json([
            'entry' => $entry,
        ]);
    }

    public function store($id)
    {
        $data = [
            'title' => request('title'),
            'slug' => request('slug'),
            'body' => request('body', ''),
            'meta' => request('meta', (object) []),
        ];

        validator($data, [
            'title' => 'required',
            'slug' => 'required|'.Rule::unique(config('wink.database_connection').'.wink_pages', 'slug')->ignore(request('id')),
        ])->validate();

        $entry = $id !== 'new' ? WinkPage::findOrFail($id) : new WinkPage(['id' => request('id')]);

        $entry->fill($data);

        $entry->save();

        return response()->json([
            'entry' => $entry,
        ]);
    }

    public function delete($id)
    {
        $entry = WinkPage::findOrFail($id);

        $entry->delete();
    }
}

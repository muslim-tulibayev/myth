<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function index()
    {
        $collections = Collection::withCount([
            'cards',
            'cards as due_count' => fn ($q) => $q->due(),
        ])->orderBy('name')->get();

        return view('collections.index', compact('collections'));
    }

    public function create()
    {
        return view('collections.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'emoji'       => ['nullable', 'string', 'max:10'],
        ]);

        $data['color'] = $data['color'] ?? Collection::randomColor();

        Collection::create($data);

        return redirect()->route('collections.index')->with('success', 'Collection created.');
    }

    public function show(Collection $collection)
    {
        $cards    = $collection->cards()->orderBy('word')->get();
        $dueCount = $collection->dueCount();

        return view('collections.show', compact('collection', 'cards', 'dueCount'));
    }

    public function edit(Collection $collection)
    {
        return view('collections.edit', compact('collection'));
    }

    public function update(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'emoji'       => ['nullable', 'string', 'max:10'],
        ]);

        $data['color'] = $data['color'] ?? $collection->color;

        $collection->update($data);

        return redirect()->route('collections.show', $collection)->with('success', 'Collection updated.');
    }
}

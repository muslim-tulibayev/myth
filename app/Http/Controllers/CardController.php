<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use Illuminate\Http\Request;

class CardController extends Controller
{
    public function index()
    {
        $cards = Card::with('collection')
            ->orderBy(Collection::select('name')->whereColumn('id', 'cards.collection_id'))
            ->orderBy('word')
            ->get();

        return view('cards.index', compact('cards'));
    }

    public function create(Collection $collection)
    {
        return view('cards.create', compact('collection'));
    }

    public function store(Request $request, Collection $collection)
    {
        $data = $request->validate([
            'word'        => ['required', 'string', 'max:255'],
            'translation' => ['required', 'string', 'max:255'],
            'example'     => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
        ]);

        $data['collection_id'] = $collection->id;

        Card::create($data);

        return redirect()->route('collections.show', $collection)->with('success', 'Card added.');
    }

    public function edit(Collection $collection, Card $card)
    {
        return view('cards.edit', compact('collection', 'card'));
    }

    public function update(Request $request, Collection $collection, Card $card)
    {
        $data = $request->validate([
            'word'        => ['required', 'string', 'max:255'],
            'translation' => ['required', 'string', 'max:255'],
            'example'     => ['nullable', 'string'],
            'notes'       => ['nullable', 'string'],
        ]);

        $card->update($data);

        return redirect()->route('collections.show', $collection)->with('success', 'Card updated.');
    }
}

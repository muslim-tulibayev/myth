<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function show(Collection $collection)
    {
        $dueCards = $collection->cards()->due()->get()->shuffle();

        if ($dueCards->isEmpty()) {
            return redirect()->route('collections.show', $collection)
                ->with('info', 'No cards due — check back tomorrow.');
        }

        $allTranslations = $collection->cards()->pluck('translation', 'id');
        $extraTranslations = Card::whereNotIn('collection_id', [$collection->id])
            ->pluck('translation')
            ->unique()
            ->values();

        $cards = $dueCards->map(function (Card $card) use ($allTranslations, $extraTranslations) {
            $distractors = $allTranslations
                ->filter(fn ($t, $id) => $id !== $card->id && $t !== $card->translation)
                ->values()
                ->shuffle()
                ->take(3)
                ->values();

            if ($distractors->count() < 3) {
                $needed = 3 - $distractors->count();
                $extra = $extraTranslations
                    ->filter(fn ($t) => $t !== $card->translation && ! $distractors->contains($t))
                    ->shuffle()
                    ->take($needed)
                    ->values();
                $distractors = $distractors->merge($extra);
            }

            $options = $distractors->push($card->translation)->shuffle()->values();

            return [
                'id'          => $card->id,
                'word'        => $card->word,
                'translation' => $card->translation,
                'example'     => $card->example,
                'options'     => $options,
            ];
        })->values();

        return view('review.show', compact('collection', 'cards'));
    }

    public function practice(Collection $collection)
    {
        $allCards = $collection->cards()->get()->shuffle();

        if ($allCards->isEmpty()) {
            return redirect()->route('collections.show', $collection)
                ->with('info', 'No cards in this collection yet.');
        }

        $allTranslations = $allCards->pluck('translation', 'id');
        $extraTranslations = Card::whereNotIn('collection_id', [$collection->id])
            ->pluck('translation')
            ->unique()
            ->values();

        $cards = $allCards->map(function (Card $card) use ($allTranslations, $extraTranslations) {
            $distractors = $allTranslations
                ->filter(fn ($t, $id) => $id !== $card->id && $t !== $card->translation)
                ->values()
                ->shuffle()
                ->take(3)
                ->values();

            if ($distractors->count() < 3) {
                $needed = 3 - $distractors->count();
                $extra = $extraTranslations
                    ->filter(fn ($t) => $t !== $card->translation && ! $distractors->contains($t))
                    ->shuffle()
                    ->take($needed)
                    ->values();
                $distractors = $distractors->merge($extra);
            }

            $options = $distractors->push($card->translation)->shuffle()->values();

            return [
                'id'          => $card->id,
                'word'        => $card->word,
                'translation' => $card->translation,
                'example'     => $card->example,
                'options'     => $options,
            ];
        })->values();

        return view('practice.show', compact('collection', 'cards'));
    }

    public function store(Request $request, Collection $collection, Card $card)
    {
        abort_unless($card->collection_id === $collection->id, 404);

        $request->validate(['correct' => ['required', 'boolean']]);

        if ($request->boolean('correct')) {
            $card->recordCorrect();
        } else {
            $card->recordWrong();
        }

        return response()->json(['level' => $card->level]);
    }
}

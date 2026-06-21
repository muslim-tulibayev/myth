<?php

use App\Http\Controllers\CardController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HabitController::class, 'index'])->name('dashboard');

Route::patch('habits/reorder', [HabitController::class, 'reorder'])->name('habits.reorder');
Route::resource('habits', HabitController::class)->only(['show']);

Route::get('habits/{habit}/logs', [HabitLogController::class, 'index'])->name('habits.logs.index');
Route::post('habits/{habit}/logs', [HabitLogController::class, 'store'])->name('habits.logs.store');
Route::delete('habits/{habit}/logs/{log}', [HabitLogController::class, 'destroy'])->name('habits.logs.destroy');

// Collections & Cards
Route::get('cards', [CardController::class, 'index'])->name('cards.index');
Route::resource('collections', CollectionController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
Route::get('collections/{collection}/cards/create', [CardController::class, 'create'])->name('collections.cards.create');
Route::post('collections/{collection}/cards', [CardController::class, 'store'])->name('collections.cards.store');
Route::get('collections/{collection}/cards/{card}/edit', [CardController::class, 'edit'])->name('collections.cards.edit');
Route::patch('collections/{collection}/cards/{card}', [CardController::class, 'update'])->name('collections.cards.update');

// Review
Route::get('collections/{collection}/review', [ReviewController::class, 'show'])->name('collections.review.show');
Route::post('collections/{collection}/review/{card}', [ReviewController::class, 'store'])->name('collections.review.store');
Route::get('collections/{collection}/practice', [ReviewController::class, 'practice'])->name('collections.practice.show');

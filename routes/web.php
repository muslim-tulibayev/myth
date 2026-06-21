<?php

use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitLogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HabitController::class, 'index'])->name('dashboard');

Route::patch('habits/reorder', [HabitController::class, 'reorder'])->name('habits.reorder');
Route::resource('habits', HabitController::class)->only(['show']);

Route::get('habits/{habit}/logs', [HabitLogController::class, 'index'])->name('habits.logs.index');
Route::post('habits/{habit}/logs', [HabitLogController::class, 'store'])->name('habits.logs.store');
Route::delete('habits/{habit}/logs/{log}', [HabitLogController::class, 'destroy'])->name('habits.logs.destroy');

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LinkController;
use App\Http\Controllers\RedirectController;

Route::get('/', function () {
    return redirect()->route('links.index');
});

Route::get('/r/{code}', [RedirectController::class, 'show'])->name('redirect.show');

Route::get('/dashboard', function () {
    return redirect()->route('links.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::prefix('dashboard/links')->name('links.')->middleware('auth')->group(function () {
    Route::get('/', [LinkController::class, 'index'])->name('index');
    Route::get('/create', [LinkController::class, 'create'])->name('create');
    Route::post('/', [LinkController::class, 'store'])->name('store');
    Route::get('/{link}/edit', [LinkController::class, 'edit'])->name('edit');
    Route::put('/{link}', [LinkController::class, 'update'])->name('update');
    Route::delete('/{link}', [LinkController::class, 'destroy'])->name('destroy');
});


require __DIR__.'/auth.php';

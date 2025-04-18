<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TodoController;


Route::prefix('todo')->group(function () {
    Route::get('/', [TodoController::class, 'index'])->name('todo.index');
    Route::post('/', [TodoController::class, 'store'])->name('todo.store');
    Route::put('/{id}', [TodoController::class, 'update'])->name('todo.update');
    Route::delete('/{id}', [TodoController::class, 'destroy'])->name('todo.destroy');
    Route::put('/{id}/complete', [TodoController::class, 'markComplete'])->name('todo.mark-complete');
});
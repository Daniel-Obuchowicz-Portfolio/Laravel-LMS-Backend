<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ClientController;

Route::get('books', [BookController::class, 'index']);
Route::get('books/{book}', [BookController::class, 'show']);
Route::post('books/{book}/borrow', [BookController::class, 'borrow']);
Route::post('books/{book}/return', [BookController::class, 'return']);

Route::get('clients', [ClientController::class, 'index']);
Route::get('clients/{client}', [ClientController::class, 'show']);
Route::post('clients', [ClientController::class, 'store']);
Route::delete('clients/{client}', [ClientController::class, 'destroy']);

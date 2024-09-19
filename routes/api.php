<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ClientController;

Route::get('books', [BookController::class, 'index']);
Route::get('book/single/{id}', [BookController::class, 'show']);
Route::get('clients', [ClientController::class, 'index']);
Route::get('client/single/{id}', [ClientController::class, 'show']);
Route::post('add/clients', [ClientController::class, 'store']);
Route::delete('remove/client/{id}', [ClientController::class, 'destroy']);
Route::post('clients/{clientId}/borrow/{bookId}', [ClientController::class, 'borrowBook']);
Route::post('books/{bookId}/return', [ClientController::class, 'returnBook']);


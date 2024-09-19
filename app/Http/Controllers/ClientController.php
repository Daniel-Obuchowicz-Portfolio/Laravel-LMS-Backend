<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Book;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // Lista klientów
    public function index()
    {
        $clients = Client::all(['first_name', 'last_name']);

        if ($clients->isEmpty()) {
            return response()->json([
                'message' => 'No clients found.'
            ], 404);  // Kod 404, jeśli nie ma klientów
        }

        return response()->json([
            'message' => 'Client list retrieved successfully.',
            'data' => $clients
        ]);
    }

    // Szczegóły klienta
    public function show($id)
    {
        // Znajdź klienta i załaduj powiązane książki
        $client = Client::with('books')->findOrFail($id);

        // Dane podstawowe klienta
        $response = [
            'first_name' => $client->first_name,
            'last_name' => $client->last_name,
        ];

        // Jeśli klient ma wypożyczone książki, dodaj 'borrowed_books' bez 'year_published' i 'publisher'
        if ($client->books->isNotEmpty()) {
            $response['borrowed_books'] = $client->books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'name' => $book->name,
                    'author' => $book->author,  // Zostawiamy tylko te pola
                ];
            });
        }

        // Zwróć dane klienta wraz z wypożyczonymi książkami
        return response()->json([
            'message' => 'Client details retrieved successfully.',
            'data' => $response
        ]);
    }

    // Dodawanie klienta
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Client created successfully.',
            'data' => $client
        ], 201);  // Kod 201 (Created) dla pomyślnego stworzenia zasobu
    }

    // Usuwanie klienta
    public function destroy($id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found.'
            ], 404);  // Kod 404, jeśli klient nie istnieje
        }

        $client->delete();

        return response()->json([
            'message' => 'Client deleted successfully.'
        ], 200);  // Kod 200 dla pomyślnego usunięcia klienta
    }

    // Wypożyczanie książki
    public function borrowBook($clientId, $bookId)
    {
        $client = Client::find($clientId);
        $book = Book::find($bookId);

        if (!$client) {
            return response()->json([
                'message' => 'Client not found.'
            ], 404);  // Kod 404, jeśli klient nie istnieje
        }

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);  // Kod 404, jeśli książka nie istnieje
        }

        if ($book->is_borrowed) {
            return response()->json([
                'message' => 'Book already borrowed.'
            ], 400);  // Kod 400, jeśli książka jest już wypożyczona
        }

        $book->client_id = $client->id;
        $book->is_borrowed = true;
        $book->save();

        // Zwróć tylko potrzebne pola
        return response()->json([
            'message' => 'Book borrowed successfully.',
            'data' => [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author
            ]
        ]);
    }

    // Oddawanie książki
    public function returnBook($bookId)
    {
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404);  // Kod 404, jeśli książka nie istnieje
        }

        if (!$book->is_borrowed) {
            return response()->json([
                'message' => 'Book is not borrowed.'
            ], 400);  // Kod 400, jeśli książka nie jest wypożyczona
        }

        $book->client_id = null;
        $book->is_borrowed = false;
        $book->save();

        // Zwróć tylko potrzebne pola
        return response()->json([
            'message' => 'Book returned successfully.',
            'data' => [
                'id' => $book->id,
                'name' => $book->name,
                'author' => $book->author
            ]
        ]);
    }
}

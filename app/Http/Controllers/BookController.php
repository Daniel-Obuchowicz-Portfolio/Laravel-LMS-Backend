<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Http\Controllers\Controller;

class BookController extends Controller
{
    // Listowanie książek z wyszukiwarką i paginacją
    public function index(Request $request)
    {
        $query = Book::query();

        // Wyszukiwanie książek na podstawie nazwy, autora lub klienta
        if ($request->has('search')) {
            $search = $request->input('search');
            $searchTerms = explode(' ', $search);

            $query->where('name', 'like', "%$search%")
                  ->orWhere('author', 'like', "%$search%")
                  ->orWhereHas('client', function ($q) use ($searchTerms) {
                      if (count($searchTerms) > 1) {
                          $q->where('first_name', 'like', "%$searchTerms[0]%")
                            ->where('last_name', 'like', "%$searchTerms[1]%");
                      } else {
                          $q->where('first_name', 'like', "%$searchTerms[0]%")
                            ->orWhere('last_name', 'like', "%$searchTerms[0]%");
                      }
                  });
        }

        // Pobierz książki i formatowanie danych
        $books = $query->with('client')->paginate(20);

        // Sprawdzenie, czy są wyniki
        if ($books->isEmpty()) {
            return response()->json([
                'message' => 'No books found for the given criteria.'
            ], 404); // Kod 404, jeśli nie ma książek
        }

        $books->getCollection()->transform(function ($book) {
            // Domyślne dane książki
            $data = [
                'name' => $book->name,
                'is_borrowed' => $book->is_borrowed, // true/false dla statusu wypożyczenia
            ];

            // Dodaj dane klienta, tylko jeśli książka jest wypożyczona
            if ($book->is_borrowed && $book->client) {
                $data['borrowed_by'] = [
                    'first_name' => $book->client->first_name,
                    'last_name' => $book->client->last_name,
                ];
            }

            return $data;
        });

        return response()->json([
            'message' => 'Books retrieved successfully.',
            'data' => $books
        ], 200); // Kod 200, jeśli książki zostały znalezione
    }

    // Szczegóły książki
    public function show($id)
    {
        $book = Book::with('client')->find($id);

        // Sprawdzenie, czy książka istnieje
        if (!$book) {
            return response()->json([
                'message' => 'Book not found.'
            ], 404); // Kod 404, jeśli książka nie istnieje
        }

        // Zwraca szczegóły książki z informacją, czy jest wypożyczona
        $response = [
            'name' => $book->name,
            'author' => $book->author,
            'year_published' => $book->year_published,
            'publisher' => $book->publisher,
            'is_borrowed' => $book->is_borrowed,  // true/false dla statusu wypożyczenia
        ];

        // Dodaj dane klienta, tylko jeśli książka jest wypożyczona
        if ($book->is_borrowed && $book->client) {
            $response['borrowed_by'] = [
                'first_name' => $book->client->first_name,
                'last_name' => $book->client->last_name,
            ];
        }

        return response()->json([
            'message' => 'Book details retrieved successfully.',
            'data' => $response
        ], 200); // Kod 200, jeśli szczegóły książki zostały zwrócone
    }
}

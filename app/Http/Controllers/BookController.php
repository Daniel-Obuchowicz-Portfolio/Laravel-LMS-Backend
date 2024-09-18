<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * List all books with pagination and optional search functionality.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('author', 'like', "%{$search}%")
                ->orWhereHas('borrowedBy', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                });
        }

        return $query->paginate(20);
    }

    /**
     * Show details of a single book.
     */
    public function show(Book $book)
    {
        return $book->load('borrowedBy');
    }

    /**
     * Borrow a book.
     */
    public function borrow(Request $request, Book $book)
    {
        if ($book->is_borrowed) {
            return response()->json(['message' => 'Book is already borrowed'], 400);
        }

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
        ]);

        $book->update([
            'is_borrowed' => true,
            'borrowed_by' => $validated['client_id'],
        ]);

        return response()->json($book);
    }

    /**
     * Return a borrowed book.
     */
    public function return(Book $book)
    {
        if (!$book->is_borrowed) {
            return response()->json(['message' => 'Book is not borrowed'], 400);
        }

        $book->update([
            'is_borrowed' => false,
            'borrowed_by' => null,
        ]);

        return response()->json($book);
    }
}

<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Book;
use App\Models\Client;
use PHPUnit\Framework\Attributes\Test;

class BookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_list_books_with_pagination_and_search(): void
    {
        // Tworzymy książki z unikalnymi nazwami
        Book::factory()->create(['name' => 'Unique Book']);
        Book::factory()->create(['name' => 'Another Book']);

        // Wysyłamy żądanie GET na endpoint `/api/books`
        $response = $this->getJson('/api/books?search=Unique&page=1');

        // Sprawdzamy, czy odpowiedź ma kod 200 OK i zawiera szukaną książkę
        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Unique Book']);
    }

    #[Test]
    public function it_can_show_book_details(): void
    {
        // Tworzymy książkę
        $book = Book::factory()->create();

        // Wysyłamy żądanie GET na endpoint `/api/book/single/{id}`
        $response = $this->getJson('/api/book/single/' . $book->id);

        // Sprawdzamy, czy odpowiedź ma kod 200 OK i zawiera szczegóły książki
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'name' => $book->name,
                     'author' => $book->author,
                 ]);
    }

    #[Test]
    public function it_returns_404_if_book_not_found(): void
    {
        // Wysyłamy żądanie GET na nieistniejący ID
        $response = $this->getJson('/api/book/single/999');

        // Sprawdzamy, czy odpowiedź ma kod 404
        $response->assertStatus(404);
    }
}

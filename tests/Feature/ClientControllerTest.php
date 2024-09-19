<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Client;
use App\Models\Book;
use PHPUnit\Framework\Attributes\Test;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_can_add_a_client(): void
    {
        // Dane klienta
        $clientData = [
            'first_name' => 'John',
            'last_name' => 'Doe'
        ];

        // Wysyłamy żądanie POST na endpoint `/api/add/clients`
        $response = $this->postJson('/api/add/clients', $clientData);

        // Sprawdzamy, czy odpowiedź ma kod 201 Created
        $response->assertStatus(201)
                 ->assertJsonFragment(['first_name' => 'John', 'last_name' => 'Doe']);
    }

    #[Test]
    public function it_can_delete_a_client(): void
    {
        // Tworzymy klienta
        $client = Client::factory()->create();

        // Wysyłamy żądanie DELETE na endpoint `/api/remove/client/{id}`
        $response = $this->deleteJson('/api/remove/client/' . $client->id);

        // Sprawdzamy, czy odpowiedź ma kod 200 OK
        $response->assertStatus(200);

        // Sprawdzamy, czy klient został usunięty
        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    #[Test]
    public function it_can_show_client_details_with_borrowed_books(): void
    {
        // Tworzymy klienta
        $client = Client::factory()->create();

        // Tworzymy książkę, która będzie wypożyczona przez klienta
        $book = Book::factory()->create(['client_id' => $client->id, 'is_borrowed' => true]);

        // Wysyłamy żądanie GET na endpoint `/api/client/single/{id}`
        $response = $this->getJson('/api/client/single/' . $client->id);

        // Sprawdzamy, czy odpowiedź ma kod 200 OK i zawiera wypożyczone książki
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'first_name' => $client->first_name,
                     'last_name' => $client->last_name,
                 ])
                 ->assertJsonStructure([
                     'data' => [
                         'borrowed_books' => [
                             '*' => ['id', 'name', 'author']
                         ]
                     ]
                 ]);
    }

    #[Test]
    public function it_can_borrow_a_book(): void
    {
        // Tworzymy klienta i książkę
        $client = Client::factory()->create();
        $book = Book::factory()->create(['is_borrowed' => false]);

        // Wysyłamy żądanie POST na endpoint `/api/clients/{clientId}/borrow/{bookId}`
        $response = $this->postJson('/api/clients/' . $client->id . '/borrow/' . $book->id);

        // Sprawdzamy, czy odpowiedź ma kod 200 OK
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Book borrowed successfully.',
                     'data' => [
                         'id' => $book->id,
                         'name' => $book->name,
                         'author' => $book->author,
                     ]
                 ]);

        // Sprawdzamy, czy książka została oznaczona jako wypożyczona
        $this->assertDatabaseHas('books', ['id' => $book->id, 'is_borrowed' => true]);
    }

    #[Test]
    public function it_can_return_a_book(): void
    {
        // Tworzymy klienta i książkę wypożyczoną przez klienta
        $client = Client::factory()->create();
        $book = Book::factory()->create(['client_id' => $client->id, 'is_borrowed' => true]);

        // Wysyłamy żądanie POST na endpoint `/api/books/{bookId}/return`
        $response = $this->postJson('/api/books/' . $book->id . '/return');

        // Sprawdzamy, czy odpowiedź ma kod 200 OK
        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'message' => 'Book returned successfully.',
                     'data' => [
                         'id' => $book->id,
                         'name' => $book->name,
                         'author' => $book->author,
                     ]
                 ]);

        // Sprawdzamy, czy książka została oznaczona jako zwrócona
        $this->assertDatabaseHas('books', ['id' => $book->id, 'is_borrowed' => false]);
    }

    #[Test]
    public function it_returns_404_if_client_not_found(): void
    {
        // Wysyłamy żądanie GET na nieistniejący ID
        $response = $this->getJson('/api/client/single/999');

        // Sprawdzamy, czy odpowiedź ma kod 404
        $response->assertStatus(404);
    }
}

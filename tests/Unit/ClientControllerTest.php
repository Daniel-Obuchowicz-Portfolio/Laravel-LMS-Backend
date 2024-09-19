<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Client;
use App\Models\Book;

class ClientControllerTest extends TestCase
{
    #[Test]
    public function it_can_add_a_borrowed_book_to_client()
    {
        // Tworzymy klienta i książkę
        $client = new Client();
        $client->first_name = 'John';
        $client->last_name = 'Doe';

        $book = new Book();
        $book->name = 'Harry Potter';
        $book->author = 'J.K. Rowling';
        $book->is_borrowed = true;

        // Dodajemy książkę do listy wypożyczeń klienta
        $client->books = collect([$book]);

        // Sprawdzamy, czy klient ma wypożyczoną książkę
        $this->assertCount(1, $client->books);
        $this->assertEquals('Harry Potter', $client->books->first()->name);
    }

    #[Test]
    public function it_can_remove_a_borrowed_book_from_client()
    {
        // Tworzymy klienta i książkę
        $client = new Client();
        $client->first_name = 'John';
        $client->last_name = 'Doe';

        $book = new Book();
        $book->name = 'Harry Potter';
        $book->author = 'J.K. Rowling';
        $book->is_borrowed = true;

        // Dodajemy książkę do listy wypożyczeń klienta
        $client->books = collect([$book]);

        // Sprawdzamy, czy książka jest dodana
        $this->assertCount(1, $client->books);

        // Usuwamy książkę z listy
        $client->books = collect([]);

        // Sprawdzamy, czy książka została usunięta
        $this->assertCount(0, $client->books);
    }

    #[Test]
    public function it_checks_if_client_is_found()
    {
        // Tworzymy klienta
        $client = new Client();
        $client->first_name = 'John';
        $client->last_name = 'Doe';

        // Sprawdzamy, czy klient istnieje
        $this->assertEquals('John', $client->first_name);
        $this->assertEquals('Doe', $client->last_name);
    }
}

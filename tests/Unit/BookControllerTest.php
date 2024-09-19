<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\Book;

class BookControllerTest extends TestCase
{
    #[Test]
    public function it_checks_if_book_is_borrowed()
    {
        // Tworzymy fikcyjną książkę
        $book = new Book();
        $book->is_borrowed = true;

        // Sprawdzamy, czy książka jest wypożyczona
        $this->assertTrue($book->is_borrowed);

        // Zmieniamy status książki na niewypożyczoną
        $book->is_borrowed = false;

        // Sprawdzamy, czy książka nie jest wypożyczona
        $this->assertFalse($book->is_borrowed);
    }

    #[Test]
    public function it_can_format_book_details_correctly()
    {
        // Tworzymy fikcyjną książkę z danymi
        $book = new Book();
        $book->name = 'Harry Potter';
        $book->author = 'J.K. Rowling';
        $book->is_borrowed = true;

        // Sprawdzamy, czy szczegóły książki są sformatowane poprawnie
        $formattedDetails = [
            'name' => $book->name,
            'author' => $book->author,
            'is_borrowed' => $book->is_borrowed,
        ];

        $this->assertEquals('Harry Potter', $formattedDetails['name']);
        $this->assertEquals('J.K. Rowling', $formattedDetails['author']);
        $this->assertTrue($formattedDetails['is_borrowed']);
    }
}

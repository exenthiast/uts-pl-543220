<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test-token')->plainTextToken;
    }

    public function test_unauthenticated_request_to_books_endpoint_returns_401(): void
    {
        $response = $this->getJson('/api/books');

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_get_list_of_books(): void
    {
        Book::factory()->create([
            'title' => 'Buku Arsitektur Cloud',
            'author' => 'Insinyur DevOps',
            'isbn' => '978-1111111111',
            'published_year' => 2024,
            'stock' => 5,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->getJson('/api/books');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    '*' => ['id', 'title', 'author', 'isbn', 'published_year', 'stock', 'created_at'],
                ],
            ]);
    }

    public function test_authenticated_user_can_create_book_and_returns_201(): void
    {
        $payload = [
            'title' => 'Panduan Lengkap Docker & Kubernetes',
            'author' => 'Solomon Hykes',
            'isbn' => '978-9999999999',
            'published_year' => 2023,
            'stock' => 10,
            'description' => 'Buku pegangan wajib praktisi DevOps.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/books', $payload);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'message' => 'Buku berhasil ditambahkan.',
                'title' => 'Panduan Lengkap Docker & Kubernetes',
                'isbn' => '978-9999999999',
            ]);

        $this->assertDatabaseHas('books', [
            'isbn' => '978-9999999999',
        ]);
    }

    public function test_create_book_fails_with_invalid_data_and_returns_422(): void
    {
        $invalidPayload = [
            'title' => '',
            'author' => '',
            'isbn' => '',
            'published_year' => 'invalid-year',
            'stock' => -5,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->postJson('/api/books', $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'author', 'isbn', 'published_year', 'stock']);
    }

    public function test_authenticated_user_can_update_book(): void
    {
        $book = Book::create([
            'title' => 'Buku Lama',
            'author' => 'Penulis Lama',
            'isbn' => '978-1234567890',
            'published_year' => 2020,
            'stock' => 2,
        ]);

        $updatePayload = [
            'title' => 'Buku Edisi Revisi',
            'author' => 'Penulis Lama',
            'isbn' => '978-1234567890',
            'published_year' => 2025,
            'stock' => 20,
            'description' => 'Edisi kedua diperbarui.',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->putJson('/api/books/'.$book->id, $updatePayload);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Buku berhasil diperbarui.',
                'title' => 'Buku Edisi Revisi',
                'stock' => 20,
            ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Buku Edisi Revisi',
        ]);
    }

    public function test_authenticated_user_can_delete_book(): void
    {
        $book = Book::create([
            'title' => 'Buku Yang Akan Dihapus',
            'author' => 'Anonim',
            'isbn' => '978-0000000000',
            'published_year' => 2021,
            'stock' => 1,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$this->token,
        ])->deleteJson('/api/books/'.$book->id);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Buku berhasil dihapus.',
            ]);

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }
}

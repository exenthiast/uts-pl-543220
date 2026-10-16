<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Default Admin User for Postman & Vue testing
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator UTS',
                'password' => Hash::make('password123'),
            ]
        );

        // Initial sample books
        $sampleBooks = [
            [
                'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
                'author' => 'Robert C. Martin',
                'isbn' => '978-0132350884',
                'published_year' => 2008,
                'stock' => 12,
                'description' => 'Panduan praktik penulisan kode yang rapi dan terstruktur.',
            ],
            [
                'title' => 'The DevOps Handbook',
                'author' => 'Gene Kim, Jez Humble, Patrick Debois',
                'isbn' => '978-1942788003',
                'published_year' => 2016,
                'stock' => 8,
                'description' => 'Cara mewujudkan efisiensi kelas dunia, keandalan, dan keamanan pada arsitektur perangkat lunak.',
            ],
            [
                'title' => 'Building Microservices: Designing Fine-Grained Systems',
                'author' => 'Sam Newman',
                'isbn' => '978-1492034025',
                'published_year' => 2021,
                'stock' => 15,
                'description' => 'Panduan komprehensif merancang dan mengoperasikan arsitektur microservices modern.',
            ],
        ];

        foreach ($sampleBooks as $bookData) {
            Book::updateOrCreate(
                ['isbn' => $bookData['isbn']],
                $bookData
            );
        }
    }
}

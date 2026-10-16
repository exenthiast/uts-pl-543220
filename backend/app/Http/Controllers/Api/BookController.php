<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $books = Book::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Daftar buku berhasil diambil.',
            'data' => $books,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn',
            'published_year' => 'required|integer|min:1800|max:2100',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $book = Book::create($validated);

        return response()->json([
            'message' => 'Buku berhasil ditambahkan.',
            'data' => $book,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'message' => 'Detail buku berhasil diambil.',
            'data' => $book,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'required|string|max:50|unique:books,isbn,'.$book->id,
            'published_year' => 'required|integer|min:1800|max:2100',
            'stock' => 'required|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $book->update($validated);

        return response()->json([
            'message' => 'Buku berhasil diperbarui.',
            'data' => $book,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $book = Book::find($id);

        if (! $book) {
            return response()->json([
                'message' => 'Buku tidak ditemukan.',
            ], 404);
        }

        $book->delete();

        return response()->json([
            'message' => 'Buku berhasil dihapus.',
        ], 200);
    }
}

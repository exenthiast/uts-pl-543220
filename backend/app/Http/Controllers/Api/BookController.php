<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $books = Book::orderBy('created_at', 'desc')->get();

            return response()->json([
                'message' => 'Daftar buku berhasil diambil.',
                'data' => $books,
            ], 200);
        } catch (Throwable $e) {
            Log::error('Koneksi basis data terputus saat mengambil daftar buku.', [
                'actor' => [
                    'user_id' => $request->user()?->id ?? 'anonymous',
                    'ip_address' => $request->ip(),
                ],
                'action' => 'database.query_execution',
                'reason' => 'DATABASE_CONNECTION_ERROR',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan sistem pada basis data.',
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'author' => 'required|string|max:255',
                'isbn' => 'required|string|max:50|unique:books,isbn',
                'published_year' => 'required|integer|min:1800|max:2100',
                'stock' => 'required|integer|min:0',
                'description' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            Log::warning('Validasi gagal saat penambahan data buku baru.', [
                'actor' => [
                    'user_id' => $request->user()?->id ?? 'unauthenticated',
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 100),
                ],
                'action' => 'book.create_validation',
                'reason' => 'VALIDATION_FAILED',
                'invalid_fields' => array_keys($e->errors()),
                'timestamp' => now()->toIso8601String(),
            ]);

            throw $e;
        }

        try {
            $book = Book::create($validated);

            Log::info('Buku baru berhasil disimpan ke dalam katalog.', [
                'actor' => [
                    'user_id' => $request->user()?->id ?? 'anonymous',
                    'email_masked' => $request->user() ? preg_replace('/(?<=.{2}).(?=.*@)/', '*', $request->user()->email) : 'guest',
                    'ip_address' => $request->ip(),
                ],
                'action' => 'book.create',
                'resource' => [
                    'book_id' => $book->id,
                    'title' => $book->title,
                    'isbn' => $book->isbn,
                ],
                'status' => 'SUCCESS',
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'message' => 'Buku berhasil ditambahkan.',
                'data' => $book,
            ], 201);
        } catch (Throwable $e) {
            Log::error('Koneksi basis data terputus atau query gagal dieksekusi.', [
                'actor' => [
                    'user_id' => $request->user()?->id ?? 'anonymous',
                    'ip_address' => $request->ip(),
                ],
                'action' => 'database.query_execution',
                'reason' => 'DATABASE_CONNECTION_ERROR',
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan sistem pada basis data.',
            ], 500);
        }
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

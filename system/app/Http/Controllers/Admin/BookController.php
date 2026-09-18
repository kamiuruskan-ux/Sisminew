<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->paginate(15);

        $categories = Book::select('category')->distinct()->pluck('category');

        return view('admin.books.index', compact('books', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover')) {
            $savedCover = save_uploaded_public_file($request->file('cover'), 'img/books/covers');
            $coverPath = 'books/covers/' . basename($savedCover);
        }

        $filePath = null;
        if ($request->hasFile('pdf_file')) {
            $savedPdf = save_uploaded_public_file($request->file('pdf_file'), 'doc/books/pdf');
            $filePath = 'doc/books/pdf/' . basename($savedPdf);
        }

        Book::create([
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'category' => $request->category,
            'isbn' => $request->isbn,
            'description' => $request->description,
            'cover_path' => $coverPath,
            'file_path' => $filePath,
            'stock' => $request->stock,
            'is_active' => $request->has('is_active') ? true : true,
        ]);

        return back()->with('success', 'Buku perpustakaan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file' => 'nullable|mimes:pdf|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'author' => $request->author,
            'publisher' => $request->publisher,
            'category' => $request->category,
            'isbn' => $request->isbn,
            'description' => $request->description,
            'stock' => $request->stock,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('cover')) {
            if ($book->cover_path) {
                delete_public_file($book->cover_path, 'img/books/covers');
            }
            $savedCover = save_uploaded_public_file($request->file('cover'), 'img/books/covers');
            $data['cover_path'] = 'books/covers/' . basename($savedCover);
        }

        if ($request->hasFile('pdf_file')) {
            if ($book->file_path) {
                delete_public_file($book->file_path, 'doc/books/pdf');
            }
            $savedPdf = save_uploaded_public_file($request->file('pdf_file'), 'doc/books/pdf');
            $data['file_path'] = 'doc/books/pdf/' . basename($savedPdf);
        }

        $book->update($data);

        return back()->with('success', 'Data buku berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        $book = Book::findOrFail($id);
        $book->is_active = !$book->is_active;
        $book->save();

        return back()->with('success', 'Status ketersediaan buku berhasil diubah.');
    }

    public function destroy($id)
    {
        $book = Book::findOrFail($id);
        
        if ($book->cover_path) {
            delete_public_file($book->cover_path, 'img/books/covers');
        }
        if ($book->file_path) {
            delete_public_file($book->file_path, 'doc/books/pdf');
        }

        $book->delete();

        return back()->with('success', 'Buku berhasil dihapus dari perpustakaan.');
    }
}

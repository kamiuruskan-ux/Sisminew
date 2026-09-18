<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    /**
     * Display E-Library digital book catalog for student.
     */
    public function index(Request $request)
    {
        $query = Book::where('is_active', true);

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        $books = $query->latest()->get();

        $categories = Book::where('is_active', true)
            ->distinct()
            ->pluck('category');

        return view('student.library.index', compact('books', 'categories'));
    }

    /**
     * Show book details and PDF viewer.
     */
    public function show($id)
    {
        $book = Book::where('is_active', true)->findOrFail($id);
        return view('student.library.show', compact('book'));
    }
}

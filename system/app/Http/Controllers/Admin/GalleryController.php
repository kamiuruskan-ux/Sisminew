<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    public function index()
    {
        $galleries = Gallery::with('category')->latest()->paginate(12);
        $categories = Category::where('type', 'gallery')->orderBy('name')->get();
        return view('admin.gallery.index', compact('galleries', 'categories'));
    }

    public function create()
    {
        return redirect()->route('admin.gallery.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:galleries',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,svg,jfif|max:10240',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['title']);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        // Handle image upload to public/img/gallery/
        if ($request->hasFile('image')) {
            $savedPath = save_uploaded_public_file($request->file('image'), 'img/gallery');
            $validated['image'] = $savedPath;
        }

        Gallery::create($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil ditambahkan.');
    }

    public function show($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $gallery = Gallery::findOrFail($id);
        return redirect()->route('admin.gallery.edit', encode_id($gallery->id));
    }

    public function edit($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $gallery = Gallery::findOrFail($id);
        $categories = Category::where('type', 'gallery')->get();
        return view('admin.gallery.edit', compact('gallery', 'categories'));
    }

    public function update(Request $request, $encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $gallery = Gallery::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:galleries,slug,' . $gallery->id,
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg,jfif|max:10240',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'event_date' => 'nullable|date',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? \Illuminate\Support\Str::slug($validated['title']);
        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : ($gallery->is_active ?? true);

        // Handle image upload to public/img/gallery/
        if ($request->hasFile('image')) {
            if ($gallery->image) {
                delete_public_file($gallery->image, 'img/gallery');
            }
            $savedPath = save_uploaded_public_file($request->file('image'), 'img/gallery');
            $validated['image'] = $savedPath;
        } else {
            unset($validated['image']);
        }

        $gallery->update($validated);

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $gallery = Gallery::findOrFail($id);
        
        if ($gallery->image) {
            delete_public_file($gallery->image, 'img/gallery');
        }

        $gallery->delete();

        return redirect()->route('admin.gallery.index')
            ->with('success', 'Foto galeri berhasil dihapus.');
    }
}

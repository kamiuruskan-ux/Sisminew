<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Post::with(['category', 'author'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('author_id', $user->id);
            });

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $totalPosts = (clone $query)->count();
        $publishedPosts = (clone $query)->where('status', 'published')->count();
        $draftPosts = (clone $query)->where('status', 'draft')->count();

        $posts = $query->latest()->paginate(12)->withQueryString();

        $categories = Category::where('type', 'post')->orderBy('name')->get();

        return view('admin.posts.index', compact(
            'posts',
            'categories',
            'totalPosts',
            'publishedPosts',
            'draftPosts'
        ));
    }

    public function create()
    {
        $categories = Category::where('type', 'post')->get();
        $tags = Tag::all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,jfif|max:10240',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['author_id'] = $validated['author_id'] ?? auth()->id();
        $validated['excerpt'] = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150);

        // Handle thumbnail upload to public/img/blog/
        if ($request->hasFile('thumbnail')) {
            $savedPath = save_uploaded_public_file($request->file('thumbnail'), 'img/blog');
            $validated['thumbnail'] = basename($savedPath);
        }

        $post = Post::create($validated);

        // Attach tags
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil diterbitkan.');
    }

    public function show($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $post = Post::findOrFail($id);
        return redirect()->route('admin.posts.edit', encode_id($post->id));
    }

    public function edit($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $post = Post::findOrFail($id);
        $categories = Category::where('type', 'post')->get();
        $tags = Tag::all();
        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    public function update(Request $request, $encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $post = Post::findOrFail($id);
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,svg,jfif|max:10240',
            'category_id' => 'required|exists:categories,id',
            'author_id' => 'nullable|exists:users,id',
            'status' => 'required|in:draft,published,archived',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'tags' => 'nullable|array',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);
        $validated['author_id'] = $validated['author_id'] ?? auth()->id();
        $validated['excerpt'] = $validated['excerpt'] ?? Str::limit(strip_tags($validated['content']), 150);

        // Handle thumbnail upload to public/img/blog/
        if ($request->hasFile('thumbnail')) {
            if ($post->thumbnail) {
                delete_public_file($post->thumbnail, 'img/blog');
            }
            $savedPath = save_uploaded_public_file($request->file('thumbnail'), 'img/blog');
            $validated['thumbnail'] = 'blog/' . basename($savedPath);
        } else {
            unset($validated['thumbnail']);
        }

        $post->update($validated);

        // Attach tags
        if (isset($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy($encodedId)
    {
        $id = is_numeric($encodedId) ? (int)$encodedId : decode_id($encodedId);
        $post = Post::findOrFail($id);
        
        if ($post->thumbnail) {
            delete_public_file($post->thumbnail, 'img/blog');
        }

        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}

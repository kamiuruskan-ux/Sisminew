<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display blog listing
     */
    public function index(Request $request)
    {
        $query = Post::with(['category', 'author', 'tags'])->published();
        
        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        
        // Filter by tag
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }
        
        // Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }
        
        $posts = $query->latest('published_at')->paginate(9);
        
        return view('landing.blog', compact('posts'));
    }

    /**
     * Display single blog post
     */
    public function show($slug)
    {
        $post = Post::with(['category', 'author', 'tags'])
            ->where('slug', $slug)
            ->firstOrFail();
        
        // Increment views
        $post->increment('views');
        
        // Get related posts (same category, excluding current post)
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('landing.blog-show', compact('post', 'relatedPosts'));
    }
}

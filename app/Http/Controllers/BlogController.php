<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;

class BlogController extends Controller
{
    /**
     * Display a listing of all published blogs.
     */
    public function index()
    {
        $blogs = Blog::with('author')
            ->where('is_published', true)
            ->latest('published_at')
            ->paginate(12);

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $blog = Blog::with('author')
            ->where('slug', $slug)
            ->where('is_published', true)
            ->firstOrFail();

        // Pass some related or latest blogs for the sidebar
        $latestBlogs = Blog::where('is_published', true)
            ->where('id', '!=', $blog->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('blogs.show', compact('blog', 'latestBlogs'));
    }
}

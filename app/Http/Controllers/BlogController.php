<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    /**
     * Display a listing of all published blogs.
     */
    public function index()
    {
        $page = request('page', 1);
        $latestUpdate = Cache::remember('blogs_latest_update', 60, function() {
            return Blog::max('updated_at');
        });
        
        $cacheKey = 'blogs_index_v4_page_' . $page . '_' . strtotime($latestUpdate);
        
        $blogs = Cache::remember($cacheKey, 3600, function() {
            return Blog::with('author')
                ->where('is_published', true)
                ->latest('published_at')
                ->paginate(12);
        });

        $blogsHtml = Cache::remember('blogs_html_' . $cacheKey, 3600, function() use ($blogs) {
            return view('blogs.partials.index_blogs', compact('blogs'))->render();
        });

        return view('blogs.index', compact('blogs', 'blogsHtml'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug)
    {
        $blog = Cache::remember('blog_model_' . $slug, 3600, function() use ($slug) {
            return Blog::with('author')
                ->where('slug', $slug)
                ->where('is_published', true)
                ->firstOrFail();
        });

        $latestBlogs = Cache::remember('blog_latest_sidebar_' . $blog->id, 3600, function() use ($blog) {
            return Blog::where('is_published', true)
                ->where('id', '!=', $blog->id)
                ->latest('published_at')
                ->take(5)
                ->get();
        });

        return view('blogs.show', compact('blog', 'latestBlogs'));
    }
}

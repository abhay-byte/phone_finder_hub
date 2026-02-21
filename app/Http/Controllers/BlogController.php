<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;
use App\Services\SEO\SeoManager;
use App\Services\SEO\SEOData;

class BlogController extends Controller
{
    /**
     * Display a listing of all published blogs.
     */
    public function index(SeoManager $seo)
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

        $seo->set(new SEOData(
            title: 'Guides & Reviews | PhoneFinderHub Blog',
            description: 'Read the latest guides, in-depth reviews, and industry news on smartphones.',
            url: route('blogs.index'),
        ));

        return view('blogs.index', compact('blogs', 'blogsHtml'));
    }

    /**
     * Display the specified blog post.
     */
    public function show($slug, SeoManager $seo)
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

        $seo->set($blog->getSEOData());

        return view('blogs.show', compact('blog', 'latestBlogs'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class ForumController extends Controller
{
    public function index()
    {
        $categoriesHtml = Cache::remember('forum_index_categories_html_v2', 300, function() {
            $categories = ForumCategory::withCount('posts')->orderBy('order', 'asc')->get();
            return view('forum.partials.index_categories', compact('categories'))->render();
        });
        
        return view('forum.index', compact('categoriesHtml'));
    }

    public function category(Request $request, $slug)
    {
        $category = Cache::remember('forum_category_' . $slug, 3600, function() use ($slug) {
            return ForumCategory::where('slug', $slug)->firstOrFail();
        });
        
        $sort = $request->input('sort', 'latest');
        $page = $request->input('page', 1);
        
        $latestUpdate = Cache::remember('forum_category_update_' . $category->id, 60, function() use ($category) {
            return ForumPost::where('forum_category_id', $category->id)->max('updated_at');
        });

        $queryCacheKey = 'forum_category_posts_v4_' . $category->id . '_' . $sort . '_page_' . $page . '_' . strtotime($latestUpdate);
        
        $posts = Cache::remember($queryCacheKey, 300, function() use ($category, $sort) {
            $query = ForumPost::where('forum_category_id', $category->id)
                ->with(['user'])
                ->withCount('comments');

            switch ($sort) {
                case 'upvotes':
                    $query->orderBy('upvotes', 'desc');
                    break;
                case 'views':
                    $query->orderBy('views', 'desc');
                    break;
                case 'comments':
                    $query->orderBy('comments_count', 'desc');
                    break;
                case 'updated':
                    $query->orderBy('updated_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            return $query->paginate(15)->appends(['sort' => $sort]);
        });
        
        $cacheKeyHtml = 'forum_category_posts_html_' . $category->id . '_' . $sort . '_page_' . $page . '_' . strtotime($latestUpdate) . '_v3';
        
        $postsHtml = Cache::remember($cacheKeyHtml, 300, function() use ($posts) {
            return view('forum.partials.category_posts', compact('posts'))->render();
        });
        
        return view('forum.category', compact('category', 'posts', 'sort', 'postsHtml'));
    }

    public function show($slug)
    {
        $post = Cache::remember('forum_post_v3_' . $slug, 60, function() use ($slug) {
            return ForumPost::where('slug', $slug)
                ->with(['user', 'category', 'comments.user'])
                ->firstOrFail();
        });
            
        // Synchronous DB writes take 4s on external Postgres. Avoiding to save 4s page load.
        // $post->increment('views');
        
        return view('forum.show', compact('post'));
    }

    public function create($slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        return view('forum.create-post', compact('category'));
    }

    public function store(Request $request, $slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $postSlug = Str::slug($request->title) . '-' . uniqid();

        $post = ForumPost::create([
            'forum_category_id' => $category->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $postSlug,
            'content' => $request->input('content'),
            'views' => 0,
        ]);

        return redirect()->route('forum.post.show', $post->slug)
            ->with('success', 'Post created successfully!');
    }

    public function reply(Request $request, $slug)
    {
        $post = ForumPost::where('slug', $slug)->firstOrFail();
        
        $request->validate([
            'content' => 'required|string',
        ]);

        ForumComment::create([
            'forum_post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('forum.post.show', $post->slug)->with('success', 'Reply posted successfully!');
    }

    public function upvote($slug)
    {
        $post = ForumPost::where('slug', $slug)->firstOrFail();
        
        // In a complex application, we'd record user votes in a pivot table to prevent double voting.
        // For simplicity as requested, we just increment the integer.
        // Or actually, simple session check to prevent immediate double votes if wanted, 
        // but just incrementing is the MVP.
        $post->increment('upvotes');
        
        return back()->with('success', 'Post upvoted!');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ForumCategory;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::withCount('posts')->orderBy('order', 'asc')->get();
        return view('forum.index', compact('categories'));
    }

    public function category(Request $request, $slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        
        $sort = $request->input('sort', 'latest');
        
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

        $posts = $query->paginate(15)->appends(['sort' => $sort]);
        
        return view('forum.category', compact('category', 'posts', 'sort'));
    }

    public function show($slug)
    {
        $post = ForumPost::where('slug', $slug)
            ->with(['user', 'category', 'comments.user'])
            ->firstOrFail();
            
        // Increment views
        $post->increment('views');
        
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

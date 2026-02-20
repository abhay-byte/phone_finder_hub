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
        $categories = ForumCategory::withCount('posts')->get();
        return view('forum.index', compact('categories'));
    }

    public function category($slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        $posts = ForumPost::where('forum_category_id', $category->id)
            ->with(['user'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('forum.category', compact('category', 'posts'));
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
            'content' => $request->content,
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
            'content' => $request->content,
        ]);

        return redirect()->route('forum.post.show', $post->slug)->with('success', 'Reply posted successfully!');
    }
}

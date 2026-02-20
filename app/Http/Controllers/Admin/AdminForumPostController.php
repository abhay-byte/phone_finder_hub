<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumPost;
use App\Models\ForumComment;
use Illuminate\Http\Request;

class AdminForumPostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index()
    {
        $posts = ForumPost::with(['category', 'user'])
            ->withCount('comments')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.forums.posts.index', compact('posts'));
    }

    /**
     * Display the specified post with its comments.
     */
    public function show($id)
    {
        $post = ForumPost::with(['category', 'user', 'comments.user'])->findOrFail($id);
        return view('admin.forums.posts.show', compact('post'));
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy($id)
    {
        $post = ForumPost::findOrFail($id);
        $post->delete();
        
        return redirect()->route('admin.forum.posts.index')->with('success', 'Forum Post and all its comments deleted successfully.');
    }

    /**
     * Remove a specific comment from a post.
     */
    public function destroyComment($id)
    {
        $comment = ForumComment::findOrFail($id);
        $postId = $comment->forum_post_id;
        $comment->delete();
        
        return redirect()->route('admin.forum.posts.show', $postId)->with('success', 'Comment deleted successfully.');
    }
}

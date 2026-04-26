<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ForumCommentRepository;
use App\Repositories\ForumPostRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminForumPostController extends Controller
{
    protected ForumPostRepository $posts;

    protected ForumCommentRepository $comments;

    public function __construct(ForumPostRepository $posts, ForumCommentRepository $comments)
    {
        $this->posts = $posts;
        $this->comments = $comments;
    }

    public function index(Request $request)
    {
        $all = $this->posts->all();
        foreach ($all as $post) {
            $post->comments_count = count($this->comments->forPost($post->id));
        }

        usort($all, function ($a, $b) {
            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });

        $page = (int) $request->input('page', 1);
        $perPage = 20;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $posts = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.forums.posts.index', compact('posts'));
    }

    public function show(string $id)
    {
        $post = $this->posts->findOrFail($id);

        return view('admin.forums.posts.show', compact('post'));
    }

    public function destroy(string $id)
    {
        $post = $this->posts->findOrFail($id);
        $this->posts->delete($post->id);

        return redirect()->route('admin.forum.posts.index')->with('success', 'Forum Post and all its comments deleted successfully.');
    }

    public function destroyComment(string $id)
    {
        $comment = $this->comments->findOrFail($id);
        $postId = $comment->forum_post_id;
        $this->comments->delete($comment->id);

        return redirect()->route('admin.forum.posts.show', $postId)->with('success', 'Comment deleted successfully.');
    }
}

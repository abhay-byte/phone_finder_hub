<?php

namespace App\Http\Controllers;

use App\Repositories\CommentRepository;
use App\Repositories\CommentUpvoteRepository;
use Illuminate\Support\Facades\Auth;

class CommentUpvoteController extends Controller
{
    protected CommentRepository $comments;

    protected CommentUpvoteRepository $upvotes;

    public function __construct(CommentRepository $comments, CommentUpvoteRepository $upvotes)
    {
        $this->comments = $comments;
        $this->upvotes = $upvotes;
    }

    public function toggle(string $commentId)
    {
        $userId = Auth::id();
        $comment = $this->comments->findOrFail($commentId);

        if ($this->upvotes->hasUpvoted($comment->id, $userId)) {
            $this->upvotes->removeUpvote($comment->id, $userId);
            $newCount = max(0, ($comment->upvotes_count ?? 0) - 1);
            $this->comments->update($comment->id, ['upvotes_count' => $newCount]);
            $status = 'removed';
        } else {
            $this->upvotes->create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
                'created_at' => now()->format('c'),
            ]);
            $newCount = ($comment->upvotes_count ?? 0) + 1;
            $this->comments->update($comment->id, ['upvotes_count' => $newCount]);
            $status = 'added';
        }

        return response()->json([
            'status' => $status,
            'upvotes_count' => $newCount,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentUpvote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentUpvoteController extends Controller
{
    /**
     * Toggle the upvote status of a comment for the auth user.
     */
    public function toggle(Comment $comment)
    {
        $userId = Auth::id();
        
        $existingUpvote = CommentUpvote::where('comment_id', $comment->id)
                                       ->where('user_id', $userId)
                                       ->first();

        if ($existingUpvote) {
            // User already upvoted, so un-upvote
            $existingUpvote->delete();
            $comment->decrement('upvotes_count');
            $status = 'removed';
        } else {
            // New upvote
            CommentUpvote::create([
                'comment_id' => $comment->id,
                'user_id' => $userId,
            ]);
            $comment->increment('upvotes_count');
            $status = 'added';
        }

        // Return JSON so we can update the UI without a page reload
        return response()->json([
            'status' => $status,
            'upvotes_count' => $comment->upvotes_count,
        ]);
    }
}

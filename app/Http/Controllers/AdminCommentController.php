<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Phone;
use Illuminate\Support\Facades\Auth;
use App\Services\ProfanityFilter;

class AdminCommentController extends Controller
{
    protected ProfanityFilter $profanityFilter;

    public function __construct(ProfanityFilter $profanityFilter)
    {
        $this->profanityFilter = $profanityFilter;
    }

    public function index(Request $request)
    {
        $query = Comment::with(['phone', 'user'])
                        ->withCount('upvotes');

        // Filter by specific phone if requested
        if ($phoneId = $request->input('phone_id')) {
            $query->where('phone_id', $phoneId);
        }

        // Search text
        if ($search = $request->input('search')) {
            $query->where('content', 'like', "%{$search}%");
        }

        // Sorting
        $sort = $request->input('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest();
        } elseif ($sort === 'most_upvoted') {
            $query->orderByDesc('upvotes_count')->latest();
        } else {
            $query->latest(); // Default newest
        }

        $comments = $query->paginate(25)->withQueryString();
        $phones = Phone::orderBy('name')->get(['id', 'name']); // For the filter dropdown

        return view('admin.comments', compact('comments', 'phones', 'sort'));
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return back()->with('success', 'Comment deleted successfully.');
    }

    public function reply(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $reply = new Comment();
        $reply->content = $this->profanityFilter->censor($validated['content']);
        $reply->phone_id = $comment->phone_id;
        $reply->user_id = Auth::id(); // Admin's user ID
        $reply->parent_id = $comment->id;
        $reply->save();

        return back()->with('success', 'Reply posted successfully.');
    }
}

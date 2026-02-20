<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Phone;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected ProfanityFilter $profanityFilter;

    public function __construct(ProfanityFilter $profanityFilter)
    {
        $this->profanityFilter = $profanityFilter;
    }

    /**
     * Fetch comments for a phone (supports AJAX sorting).
     */
    public function index(Request $request, Phone $phone)
    {
        $sort = $request->input('sort', 'newest'); // default

        $query = $phone->comments()
            ->whereNull('parent_id')
            ->with(['user', 'replies.user', 'upvotes']);

        switch ($sort) {
            case 'top':
                $query->orderByDesc('upvotes_count')->latest();
                break;
            case 'oldest':
                $query->oldest();
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $comments = $query->get();

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('partials.comments-list-ajax', compact('comments', 'phone'))->render()
            ]);
        }

        // Fallback for direct load
        return view('partials.comments', compact('comments', 'phone'));
    }
    /**
     * Store a newly created comment or reply.
     */
    public function store(Request $request, Phone $phone)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        $comment = new Comment();
        $comment->content = $this->profanityFilter->censor($validated['content']);
        $comment->phone_id = $phone->id;
        $comment->user_id = Auth::id(); // Will be null for anonymous users
        
        if (!empty($validated['parent_id'])) {
            // Verify the parent actually belongs to the same phone
            $parent = Comment::findOrFail($validated['parent_id']);
            if ($parent->phone_id !== $phone->id) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Invalid reply target.'], 403);
                }
                return back()->with('error', 'Invalid reply target.');
            }
            $comment->parent_id = $parent->id;
        }

        $comment->save();

        if ($request->wantsJson()) {
            // Return JSON success, the frontend will trigger a re-fetch of the comments list
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment posted successfully!');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        // Must be the owner to edit
        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $comment->update([
            'content' => $this->profanityFilter->censor($validated['content'])
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment updated.');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        // Must be the owner OR a super admin to delete
        if ($comment->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $comment->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment deleted.');
    }
}

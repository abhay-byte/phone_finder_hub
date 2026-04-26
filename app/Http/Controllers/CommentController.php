<?php

namespace App\Http\Controllers;

use App\Repositories\CommentRepository;
use App\Repositories\PhoneRepository;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected ProfanityFilter $profanityFilter;

    protected CommentRepository $comments;

    protected PhoneRepository $phones;

    public function __construct(ProfanityFilter $profanityFilter, CommentRepository $comments, PhoneRepository $phones)
    {
        $this->profanityFilter = $profanityFilter;
        $this->comments = $comments;
        $this->phones = $phones;
    }

    public function index(Request $request, string $phoneId)
    {
        $phone = $this->phones->findOrFail($phoneId);
        $sort = $request->input('sort', 'newest');

        $comments = $this->comments->forPhone($phone->id);

        switch ($sort) {
            case 'top':
                usort($comments, function ($a, $b) {
                    $cmp = ($b->upvotes_count ?? 0) <=> ($a->upvotes_count ?? 0);
                    if ($cmp !== 0) {
                        return $cmp;
                    }

                    return ($b->created_at ?? '') <=> ($a->created_at ?? '');
                });
                break;
            case 'oldest':
                usort($comments, function ($a, $b) {
                    return ($a->created_at ?? '') <=> ($b->created_at ?? '');
                });
                break;
            case 'newest':
            default:
                usort($comments, function ($a, $b) {
                    return ($b->created_at ?? '') <=> ($a->created_at ?? '');
                });
                break;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'html' => view('partials.comments-list-ajax', compact('comments', 'phone'))->render(),
                'total_count' => $this->comments->countForPhone($phone->id),
            ]);
        }

        return view('partials.comments', compact('comments', 'phone'));
    }

    public function store(Request $request, string $phoneId)
    {
        $phone = $this->phones->findOrFail($phoneId);

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if (preg_match('/(https?:\/\/[^\s]+)|(www\.[^\s]+)|([a-zA-Z0-9\-]+\.(com|org|net|co|io|me|info|biz)\b)/i', $value)) {
                        $fail('Links are not allowed in comments.');
                    }
                },
            ],
            'parent_id' => 'nullable|string',
        ]);

        $data = [
            'content' => $this->profanityFilter->censor($validated['content']),
            'phone_id' => $phone->id,
            'user_id' => Auth::id(),
            'parent_id' => null,
            'upvotes_count' => 0,
            'created_at' => now()->format('c'),
        ];

        if (! empty($validated['parent_id'])) {
            $parent = $this->comments->findOrFail($validated['parent_id']);
            if ($parent->phone_id !== $phone->id) {
                if ($request->wantsJson()) {
                    return response()->json(['error' => 'Invalid reply target.'], 403);
                }

                return back()->with('error', 'Invalid reply target.');
            }
            $data['parent_id'] = $parent->id;
        }

        $this->comments->create($data);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment posted successfully!');
    }

    public function update(Request $request, string $commentId)
    {
        $comment = $this->comments->findOrFail($commentId);

        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'content' => [
                'required',
                'string',
                'max:2000',
                function ($attribute, $value, $fail) {
                    if (preg_match('/(https?:\/\/[^\s]+)|(www\.[^\s]+)|([a-zA-Z0-9\-]+\.(com|org|net|co|io|me|info|biz)\b)/i', $value)) {
                        $fail('Links are not allowed in comments.');
                    }
                },
            ],
        ]);

        $this->comments->update($comment->id, [
            'content' => $this->profanityFilter->censor($validated['content']),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment updated.');
    }

    public function destroy(string $commentId)
    {
        $comment = $this->comments->findOrFail($commentId);

        if ($comment->user_id !== Auth::id() && ! Auth::user()->isSuperAdmin()) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $this->comments->delete($comment->id);

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Comment deleted.');
    }
}

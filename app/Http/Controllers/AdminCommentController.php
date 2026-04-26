<?php

namespace App\Http\Controllers;

use App\Repositories\CommentRepository;
use App\Repositories\PhoneRepository;
use App\Services\ProfanityFilter;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class AdminCommentController extends Controller
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

    public function index(Request $request)
    {
        $all = $this->comments->all();

        if ($phoneId = $request->input('phone_id')) {
            $all = array_filter($all, fn ($c) => $c->phone_id === $phoneId);
        }

        if ($search = $request->input('search')) {
            $lower = strtolower($search);
            $all = array_filter($all, fn ($c) => str_contains(strtolower($c->content), $lower));
        }

        $sort = $request->input('sort', 'newest');
        usort($all, function ($a, $b) use ($sort) {
            if ($sort === 'oldest') {
                return ($a->created_at ?? '') <=> ($b->created_at ?? '');
            }
            if ($sort === 'most_upvoted') {
                $cmp = ($b->upvotes_count ?? 0) <=> ($a->upvotes_count ?? 0);
                if ($cmp !== 0) {
                    return $cmp;
                }
            }

            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });

        $page = (int) $request->input('page', 1);
        $perPage = 25;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $comments = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $phones = $this->phones->all();

        return view('admin.comments', compact('comments', 'phones', 'sort'));
    }

    public function destroy(string $commentId)
    {
        $this->comments->delete($commentId);

        return back()->with('success', 'Comment deleted successfully.');
    }

    public function reply(Request $request, string $commentId)
    {
        $comment = $this->comments->findOrFail($commentId);

        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $this->comments->create([
            'content' => $this->profanityFilter->censor($validated['content']),
            'phone_id' => $comment->phone_id,
            'user_id' => Auth::id(),
            'parent_id' => $comment->id,
            'upvotes_count' => 0,
            'created_at' => now()->format('c'),
        ]);

        return back()->with('success', 'Reply posted successfully.');
    }
}

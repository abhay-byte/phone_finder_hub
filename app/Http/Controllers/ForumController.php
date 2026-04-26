<?php

namespace App\Http\Controllers;

use App\Repositories\ForumCategoryRepository;
use App\Repositories\ForumCommentRepository;
use App\Repositories\ForumPostRepository;
use App\Services\SEO\SEOData;
use App\Services\SEO\SeoManager;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ForumController extends Controller
{
    protected ForumCategoryRepository $categories;

    protected ForumPostRepository $posts;

    protected ForumCommentRepository $comments;

    public function __construct(
        ForumCategoryRepository $categories,
        ForumPostRepository $posts,
        ForumCommentRepository $comments
    ) {
        $this->categories = $categories;
        $this->posts = $posts;
        $this->comments = $comments;
    }

    public function index(SeoManager $seo)
    {
        $categoriesHtml = Cache::remember('forum_index_categories_html_v3', 300, function () {
            $categories = $this->categories->ordered();
            foreach ($categories as $category) {
                $category->posts_count = count($this->posts->forCategory($category->id));
            }

            return view('forum.partials.index_categories', compact('categories'))->render();
        });

        $seo->set(new SEOData(
            title: 'Phone Finder Forums | Community & Discussions',
            description: 'Join the community discussions about the latest smartphones, tech news, reviews, and troubleshooting.',
            url: route('forum.index'),
        ));

        return view('forum.index', compact('categoriesHtml'));
    }

    public function category(Request $request, string $slug, SeoManager $seo)
    {
        $category = Cache::remember('forum_category_v2_'.$slug, 3600, function () use ($slug) {
            return $this->categories->where('slug', '==', $slug)->first() ?? abort(404);
        });

        $sort = $request->input('sort', 'latest');
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = 'forum_category_posts_v5_'.$category->id.'_'.$sort.'_page_'.$page;

        $posts = Cache::remember($cacheKey, 300, function () use ($category, $sort, $page, $perPage) {
            $all = $this->posts->forCategory($category->id);

            foreach ($all as $post) {
                $post->comments_count = count($this->comments->forPost($post->id));
            }

            usort($all, function ($a, $b) use ($sort) {
                return match ($sort) {
                    'upvotes' => ($b->upvotes ?? 0) <=> ($a->upvotes ?? 0),
                    'views' => ($b->views ?? 0) <=> ($a->views ?? 0),
                    'comments' => ($b->comments_count ?? 0) <=> ($a->comments_count ?? 0),
                    'updated' => ($b->updated_at ?? '') <=> ($a->updated_at ?? ''),
                    'oldest' => ($a->created_at ?? '') <=> ($b->created_at ?? ''),
                    default => ($b->created_at ?? '') <=> ($a->created_at ?? ''),
                };
            });

            $total = count($all);
            $items = array_slice($all, ($page - 1) * $perPage, $perPage);

            return new LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $page,
                ['path' => request()->url(), 'query' => request()->query()]
            );
        });

        $cacheKeyHtml = 'forum_category_posts_html_v4_'.$category->id.'_'.$sort.'_page_'.$page;

        $postsHtml = Cache::remember($cacheKeyHtml, 300, function () use ($posts) {
            return view('forum.partials.category_posts', compact('posts'))->render();
        });

        $seo->set(new SEOData(
            title: "{$category->name} Forum | PhoneFinderHub",
            description: $category->description ?? "Discussions related to {$category->name}.",
            url: route('forum.category', $category->slug),
        ));

        return view('forum.category', compact('category', 'posts', 'sort', 'postsHtml'));
    }

    public function show(string $slug, SeoManager $seo)
    {
        $post = Cache::remember('forum_post_v4_'.$slug, 60, function () use ($slug) {
            return $this->posts->findBySlug($slug) ?? abort(404);
        });

        $seo->set(new SEOData(
            title: "{$post->title} | PhoneFinderHub Forums",
            description: Str::limit(strip_tags($post->content), 150),
            url: route('forum.post.show', $post->slug),
            type: 'article',
        ));

        return view('forum.show', compact('post'));
    }

    public function create(string $slug)
    {
        $category = $this->categories->where('slug', '==', $slug)->first() ?? abort(404);

        return view('forum.create-post', compact('category'));
    }

    public function store(Request $request, string $slug)
    {
        $category = $this->categories->where('slug', '==', $slug)->first() ?? abort(404);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $postSlug = Str::slug($request->title).'-'.uniqid();

        $post = $this->posts->create([
            'forum_category_id' => $category->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'slug' => $postSlug,
            'content' => $request->input('content'),
            'views' => 0,
            'upvotes' => 0,
            'created_at' => now()->format('c'),
            'updated_at' => now()->format('c'),
        ]);

        return redirect()->route('forum.post.show', $post->slug)
            ->with('success', 'Post created successfully!');
    }

    public function reply(Request $request, string $slug)
    {
        $post = $this->posts->findBySlug($slug) ?? abort(404);

        $request->validate([
            'content' => 'required|string',
        ]);

        $this->comments->create([
            'forum_post_id' => $post->id,
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
            'created_at' => now()->format('c'),
        ]);

        return redirect()->route('forum.post.show', $post->slug)->with('success', 'Reply posted successfully!');
    }

    public function upvote(string $slug)
    {
        $post = $this->posts->findBySlug($slug) ?? abort(404);

        $newUpvotes = ($post->upvotes ?? 0) + 1;
        $this->posts->update($post->id, ['upvotes' => $newUpvotes]);

        return back()->with('success', 'Post upvoted!');
    }
}

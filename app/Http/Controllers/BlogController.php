<?php

namespace App\Http\Controllers;

use App\Repositories\BlogRepository;
use App\Services\SEO\SEOData;
use App\Services\SEO\SeoManager;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    protected BlogRepository $blogs;

    public function __construct(BlogRepository $blogs)
    {
        $this->blogs = $blogs;
    }

    public function index(SeoManager $seo)
    {
        $page = (int) request('page', 1);
        $perPage = 12;

        $cacheKey = 'blogs_index_v5_page_'.$page;

        $blogs = Cache::remember($cacheKey, 3600, function () use ($page, $perPage) {
            $all = $this->blogs->published()->all();
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

        $blogsHtml = Cache::remember('blogs_html_'.$cacheKey, 3600, function () use ($blogs) {
            return view('blogs.partials.index_blogs', compact('blogs'))->render();
        });

        $seo->set(new SEOData(
            title: 'Guides & Reviews | PhoneFinderHub Blog',
            description: 'Read the latest guides, in-depth reviews, and industry news on smartphones.',
            url: route('blogs.index'),
        ));

        return view('blogs.index', compact('blogs', 'blogsHtml'));
    }

    public function show($slug, SeoManager $seo)
    {
        $blog = Cache::remember('blog_model_v2_'.$slug, 3600, function () use ($slug) {
            return $this->blogs->findBySlug($slug) ?? abort(404);
        });

        $latestBlogs = Cache::remember('blog_latest_sidebar_v2_'.$blog->id, 3600, function () use ($blog) {
            $all = $this->blogs->published();

            return collect(array_slice(array_filter($all->all(), function ($b) use ($blog) {
                return $b->id !== $blog->id;
            }), 0, 5));
        });

        $seo->set($blog->getSEOData());

        return view('blogs.show', compact('blog', 'latestBlogs'));
    }
}

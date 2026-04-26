<?php

namespace App\Http\Controllers;

use App\Repositories\BlogRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBlogController extends Controller
{
    protected BlogRepository $blogs;

    public function __construct(BlogRepository $blogs)
    {
        $this->blogs = $blogs;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $all = $this->blogs->all();

        if (! auth()->user()->isSuperAdmin()) {
            $all = array_filter($all, fn ($blog) => $blog->user_id === auth()->id());
        }

        if ($search) {
            $lower = strtolower($search);
            $all = array_filter($all, fn ($blog) => str_contains(strtolower($blog->title), $lower));
        }

        usort($all, function ($a, $b) {
            return ($b->created_at ?? '') <=> ($a->created_at ?? '');
        });

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $blogs = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        return view('admin.blogs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'featured_image_url' => 'nullable|url|max:1024',
            'is_published' => 'nullable|boolean',
        ]);

        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while ($this->blogs->findBySlug($slug)) {
            $slug = $baseSlug.'-'.$counter++;
        }

        $data = [
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? Str::words(strip_tags($validated['content']), 30),
            'user_id' => auth()->id(),
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now()->format('c') : null,
            'created_at' => now()->format('c'),
        ];

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blogs', 'public');
            $data['featured_image'] = '/storage/'.$path;
        } elseif (! empty($validated['featured_image_url'])) {
            $data['featured_image'] = $validated['featured_image_url'];
        }

        $this->blogs->create($data);
        Cache::forget('latest_blogs_home');

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    public function edit(string $blogId)
    {
        $blog = $this->blogs->findOrFail($blogId);

        if (! auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.blogs.edit', compact('blog'));
    }

    public function update(Request $request, string $blogId)
    {
        $blog = $this->blogs->findOrFail($blogId);

        if (! auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'featured_image_url' => 'nullable|url|max:1024',
            'is_published' => 'nullable|boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'content' => $validated['content'],
            'excerpt' => $validated['excerpt'] ?? Str::words(strip_tags($validated['content']), 30),
        ];

        $publishing = $request->has('is_published');
        if ($publishing && ! ($blog->is_published ?? false)) {
            $data['is_published'] = true;
            $data['published_at'] = now()->format('c');
        } elseif (! $publishing) {
            $data['is_published'] = false;
            $data['published_at'] = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($blog->featured_image && str_starts_with($blog->featured_image, '/storage/')) {
                $relativePath = str_replace('/storage/', '', $blog->featured_image);
                Storage::disk('public')->delete($relativePath);
            }
            $path = $request->file('featured_image')->store('blogs', 'public');
            $data['featured_image'] = '/storage/'.$path;
        } elseif (! empty($validated['featured_image_url'])) {
            if ($blog->featured_image && str_starts_with($blog->featured_image, '/storage/')) {
                $relativePath = str_replace('/storage/', '', $blog->featured_image);
                Storage::disk('public')->delete($relativePath);
            }
            $data['featured_image'] = $validated['featured_image_url'];
        }

        $this->blogs->update($blog->id, $data);
        Cache::forget('latest_blogs_home');

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    public function destroy(string $blogId)
    {
        $blog = $this->blogs->findOrFail($blogId);

        if (! auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        $this->blogs->delete($blog->id);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
        ]);

        $path = $request->file('image')->store('blogs/content', 'public');

        return response()->json([
            'url' => Storage::url($path),
        ]);
    }
}

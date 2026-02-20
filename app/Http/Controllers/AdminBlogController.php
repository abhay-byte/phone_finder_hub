<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class AdminBlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $query = Blog::with('author');

        // Non-super-admins (authors) can only manage their own blogs
        if (!auth()->user()->isSuperAdmin()) {
            $query->where('user_id', auth()->id());
        }

        if ($search) {
            $query->where('title', 'like', "%{$search}%");
        }

        $blogs = $query->latest()->paginate(15)->withQueryString();

        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.blogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048', // max 2MB
            'is_published' => 'nullable|boolean',
        ]);

        $blog = new Blog();
        $blog->title = $validated['title'];
        
        // Generate a unique slug
        $baseSlug = Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;
        while (Blog::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }
        $blog->slug = $slug;
        
        $blog->content = $validated['content'];
        $blog->excerpt = $validated['excerpt'] ?? Str::words(strip_tags($validated['content']), 30);
        $blog->user_id = auth()->id();
        $blog->is_published = $request->has('is_published');
        if ($blog->is_published) {
            $blog->published_at = \Carbon\Carbon::now();
        }

        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('blogs', 'public');
            $blog->featured_image = '/storage/' . $path;
        }

        $blog->save();

        Cache::forget('latest_blogs_home');

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        // Check authorization: SuperAdmin or the original author
        if (!auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        return view('admin.blogs.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        if (!auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'is_published' => 'nullable|boolean',
        ]);

        $blog->title = $validated['title'];
        
        // Only trigger new slug if title changed heavily, but we'll leave slug manual or identical normally.
        // For simplicity and SEO, usually slugs shouldn't change, but if they want to:
        if ($blog->isDirty('title')) {
            $baseSlug = Str::slug($validated['title']);
            $slug = $baseSlug;
            $counter = 1;
            while (Blog::where('slug', $slug)->where('id', '!=', $blog->id)->exists()) {
                $slug = $baseSlug . '-' . $counter++;
            }
            $blog->slug = $slug;
        }

        $blog->content = $validated['content'];
        $blog->excerpt = $validated['excerpt'] ?? Str::words(strip_tags($validated['content']), 30);
        
        $publishing = $request->has('is_published');
        if ($publishing && !$blog->is_published) {
            $blog->is_published = true;
            $blog->published_at = \Carbon\Carbon::now();
        } elseif (!$publishing) {
            $blog->is_published = false;
            $blog->published_at = null;
        }

        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($blog->featured_image) {
                // ... assuming local storage ...
                $relativePath = str_replace('/storage/', '', $blog->featured_image);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
            }
            $path = $request->file('featured_image')->store('blogs', 'public');
            $blog->featured_image = '/storage/' . $path; // Changed to match store method's output format
        }

        $blog->save();

        Cache::forget('latest_blogs_home'); // Added this line

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        if (!auth()->user()->isSuperAdmin() && $blog->user_id !== auth()->id()) {
            abort(403);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Handle image uploads directly from the rich text editor (e.g., Quill).
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,webp,gif|max:2048'
        ]);

        $path = $request->file('image')->store('blogs/content', 'public');
        
        return response()->json([
            'url' => Storage::url($path)
        ]);
    }
}

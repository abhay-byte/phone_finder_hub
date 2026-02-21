<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminForumCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = ForumCategory::withCount('posts')->orderBy('order', 'asc')->paginate(15);
        return view('admin.forums.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.forums.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'rules_banner' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while (ForumCategory::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        ForumCategory::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'rules_banner' => $request->rules_banner,
        ]);

        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ForumCategory $category)
    {
        return view('admin.forums.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ForumCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'rules_banner' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        
        // Ensure slug is unique if changed
        if ($slug !== $category->slug) {
            $originalSlug = $slug;
            $counter = 1;
            while (ForumCategory::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'rules_banner' => $request->rules_banner,
        ]);

        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ForumCategory $category)
    {
        $category->delete();
        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category deleted successfully.');
    }
}

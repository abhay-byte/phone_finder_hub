<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\ForumCategoryRepository;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class AdminForumCategoryController extends Controller
{
    protected ForumCategoryRepository $categories;

    public function __construct(ForumCategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    public function index(Request $request)
    {
        $all = $this->categories->ordered();
        foreach ($all as $category) {
            $category->posts_count = 0; // Could compute from repository if needed
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $total = count($all);
        $items = array_slice($all, ($page - 1) * $perPage, $perPage);

        $categories = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.forums.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.forums.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'rules_banner' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        $originalSlug = $slug;
        $counter = 1;
        while ($this->categories->where('slug', '==', $slug)->first()) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        $this->categories->create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'rules_banner' => $request->rules_banner,
            'created_at' => now()->format('c'),
        ]);

        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category created successfully.');
    }

    public function edit(string $categoryId)
    {
        $category = $this->categories->findOrFail($categoryId);

        return view('admin.forums.categories.edit', compact('category'));
    }

    public function update(Request $request, string $categoryId)
    {
        $category = $this->categories->findOrFail($categoryId);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer',
            'rules_banner' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        if ($slug !== ($category->slug ?? '')) {
            $originalSlug = $slug;
            $counter = 1;
            while ($this->categories->where('slug', '==', $slug)->first()?->id !== $category->id) {
                $slug = $originalSlug.'-'.$counter;
                $counter++;
            }
        }

        $this->categories->update($category->id, [
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'rules_banner' => $request->rules_banner,
        ]);

        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category updated successfully.');
    }

    public function destroy(string $categoryId)
    {
        $this->categories->delete($categoryId);

        return redirect()->route('admin.forum.categories.index')->with('success', 'Forum Category deleted successfully.');
    }
}

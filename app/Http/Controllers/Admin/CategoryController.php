<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $this->authorize('view categories');

        $query = Category::withCount('articles')
            ->with('parent')
            ->orderBy('sort_order')
            ->orderBy('name');

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filter by parent
        if ($request->filled('parent')) {
            if ($request->parent === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent);
            }
        }

        $categories = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Categories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search', 'parent']),
            'parentCategories' => Category::whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $this->authorize('create categories');

        return Inertia::render('Admin/Categories/Create', [
            'parentCategories' => Category::whereNull('parent_id')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create categories');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Set default sort order if not provided
        if (!isset($validated['sort_order'])) {
            $maxOrder = Category::where('parent_id', $validated['parent_id'] ?? null)
                ->max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        Category::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
            'parent_id' => $validated['parent_id'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'] ?? true,
            'meta' => [
                'title' => $validated['meta_title'],
                'description' => $validated['meta_description'],
            ],
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $this->authorize('view categories');

        // Load parent and children relationships
        $category->load(['parent', 'children']);

        // Get recent articles separately to avoid ambiguous column issues
        $recentArticles = $category->articles()
            ->with('author')
            ->select('articles.*')
            ->orderBy('articles.created_at', 'desc')
            ->limit(10)
            ->get();

        // Add articles to the category object
        $category->setRelation('articles', $recentArticles);

        return Inertia::render('Admin/Categories/Show', [
            'category' => $category,
            'stats' => [
                'articles_count' => $category->articles()->count(),
                'published_articles' => $category->articles()->where('articles.status', 'published')->count(),
                'children_count' => $category->children()->count(),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $this->authorize('edit categories');

        return Inertia::render('Admin/Categories/Edit', [
            'category' => $category,
            'parentCategories' => Category::whereNull('parent_id')
                ->where('id', '!=', $category->id)
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('edit categories');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:categories,id',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Prevent setting parent to self or descendant
        if ($validated['parent_id'] && $this->isDescendant($category, $validated['parent_id'])) {
            return back()->withErrors(['parent_id' => 'Cannot set parent to self or descendant category.']);
        }

        $category->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'color' => $validated['color'],
            'icon' => $validated['icon'],
            'parent_id' => $validated['parent_id'],
            'sort_order' => $validated['sort_order'],
            'is_active' => $validated['is_active'] ?? true,
            'meta' => [
                'title' => $validated['meta_title'],
                'description' => $validated['meta_description'],
            ],
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete categories');

        // Check if category has articles
        if ($category->articles()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with articles. Please move or delete articles first.']);
        }

        // Check if category has children
        if ($category->children()->count() > 0) {
            return back()->withErrors(['category' => 'Cannot delete category with subcategories. Please delete subcategories first.']);
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Update the sort order of categories.
     */
    public function updateOrder(Request $request)
    {
        $this->authorize('edit categories');

        $validated = $request->validate([
            'categories' => 'required|array',
            'categories.*.id' => 'required|exists:categories,id',
            'categories.*.sort_order' => 'required|integer|min:0',
        ]);

        foreach ($validated['categories'] as $categoryData) {
            Category::where('id', $categoryData['id'])
                ->update(['sort_order' => $categoryData['sort_order']]);
        }

        return response()->json(['message' => 'Category order updated successfully.']);
    }

    /**
     * Toggle category active status.
     */
    public function toggleActive(Category $category)
    {
        $this->authorize('edit categories');

        $category->update(['is_active' => !$category->is_active]);

        $status = $category->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Category {$status} successfully.");
    }

    /**
     * Get category tree for hierarchical display.
     */
    public function tree()
    {
        $this->authorize('view categories');

        $categories = Category::with('children')
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }

    /**
     * Check if a category is a descendant of another category.
     */
    private function isDescendant(Category $category, int $potentialParentId): bool
    {
        if ($category->id === $potentialParentId) {
            return true;
        }

        foreach ($category->children as $child) {
            if ($this->isDescendant($child, $potentialParentId)) {
                return true;
            }
        }

        return false;
    }
}

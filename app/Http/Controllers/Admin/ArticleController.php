<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the articles.
     */
    public function index(Request $request)
    {
        $this->authorize('view articles');

        $query = Article::with(['author', 'categories', 'tags', 'games', 'media'])
            ->withCount(['views', 'comments', 'reactions as likes_count'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
            });
        }

        $articles = $query->paginate(15)->withQueryString();

        // Transform articles to include image URLs and view counts
        $articles->getCollection()->transform(function ($article) {
            $article->featured_image_url = $article->featured_image_url;
            $article->card_image_url = $article->card_image_url;
            $article->thumbnail_url = $article->thumbnail_url;
            
            // Use the views_count from withCount, or fallback to unique views
            if (!isset($article->views_count)) {
                $article->views_count = $article->views_count ?? 0;
            }
            
            return $article;
        });

        return Inertia::render('Admin/Articles/Index', [
            'articles' => $articles,
            'filters' => $request->only(['status', 'category', 'search']),
            'categories' => Category::active()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    /**
     * Show the form for creating a new article.
     */
    public function create()
    {
        $this->authorize('create articles');

        return Inertia::render('Admin/Articles/Create', [
            'categories' => Category::active()->orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'games' => Game::orderBy('title')->get(),
        ]);
    }

    /**
     * Store a newly created article in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create articles');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending_review,published,scheduled',
            'featured_image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'games' => 'array',
            'games.*' => 'exists:games,id',
        ]);

        // Calculate reading time (average 200 words per minute)
        $wordCount = str_word_count(strip_tags($validated['content']));
        $readingTime = max(1, round($wordCount / 200));

        $article = Article::create([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'featured_image' => $validated['featured_image'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === 'published' ? now() : null),
            'reading_time' => $readingTime,
            'author_id' => Auth::id(),
            'meta' => [
                'title' => $validated['meta_title'] ?? null,
                'description' => $validated['meta_description'] ?? null,
            ],
        ]);

        // Attach relationships
        if (!empty($validated['categories'])) {
            $article->categories()->attach($validated['categories']);
        }

        if (!empty($validated['tags'])) {
            $article->tags()->attach($validated['tags']);
            // Update tag usage counts
            Tag::whereIn('id', $validated['tags'])->increment('usage_count');
        }

        if (!empty($validated['games'])) {
            $article->games()->attach($validated['games']);
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article created successfully.');
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article)
    {
        $this->authorize('view articles');

        $article->load(['author', 'categories', 'tags', 'games', 'comments.user']);

        return Inertia::render('Admin/Articles/Show', [
            'article' => $article,
        ]);
    }

    /**
     * Show the form for editing the specified article.
     */
    public function edit(Article $article)
    {
        $this->authorize('edit articles');

        $article->load(['categories', 'tags', 'games']);

        return Inertia::render('Admin/Articles/Edit', [
            'article' => $article,
            'categories' => Category::active()->orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
            'games' => Game::orderBy('title')->get(),
        ]);
    }

    /**
     * Update the specified article in storage.
     */
    public function update(Request $request, Article $article)
    {
        $this->authorize('edit articles');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'status' => 'required|in:draft,pending_review,published,scheduled',
            'featured_image' => 'nullable|string',
            'published_at' => 'nullable|date',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'categories' => 'array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
            'games' => 'array',
            'games.*' => 'exists:games,id',
        ]);

        // Calculate reading time
        $wordCount = str_word_count(strip_tags($validated['content']));
        $readingTime = max(1, round($wordCount / 200));

        // Update published_at if status changed to published
        if ($validated['status'] === 'published' && $article->status !== 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $article->update([
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'status' => $validated['status'],
            'featured_image' => $validated['featured_image'],
            'published_at' => $validated['published_at'],
            'reading_time' => $readingTime,
            'meta' => [
                'title' => $validated['meta_title'] ?? null,
                'description' => $validated['meta_description'] ?? null,
            ],
        ]);

        // Update relationships
        $oldTagIds = $article->tags()->pluck('tags.id')->toArray();
        $newTagIds = $validated['tags'] ?? [];

        // Sync categories
        $article->categories()->sync($validated['categories'] ?? []);

        // Sync tags and update usage counts
        $article->tags()->sync($newTagIds);
        
        // Decrement old tags, increment new tags
        $removedTags = array_diff($oldTagIds, $newTagIds);
        $addedTags = array_diff($newTagIds, $oldTagIds);
        
        if (!empty($removedTags)) {
            Tag::whereIn('id', $removedTags)->decrement('usage_count');
        }
        if (!empty($addedTags)) {
            Tag::whereIn('id', $addedTags)->increment('usage_count');
        }

        // Sync games
        $article->games()->sync($validated['games'] ?? []);

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article updated successfully.');
    }

    /**
     * Remove the specified article from storage.
     */
    public function destroy(Article $article)
    {
        $this->authorize('delete articles');

        // Decrement tag usage counts
        $tagIds = $article->tags()->pluck('tags.id')->toArray();
        if (!empty($tagIds)) {
            Tag::whereIn('id', $tagIds)->decrement('usage_count');
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Article deleted successfully.');
    }

    /**
     * Bulk actions for articles.
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('edit articles');

        $validated = $request->validate([
            'action' => 'required|in:publish,unpublish,delete',
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id',
        ]);

        $articles = Article::whereIn('id', $validated['articles']);

        switch ($validated['action']) {
            case 'publish':
                $articles->update([
                    'status' => 'published',
                    'published_at' => now(),
                ]);
                $message = 'Articles published successfully.';
                break;

            case 'unpublish':
                $articles->update([
                    'status' => 'draft',
                    'published_at' => null,
                ]);
                $message = 'Articles unpublished successfully.';
                break;

            case 'delete':
                $this->authorize('delete articles');
                
                // Update tag usage counts before deletion
                foreach ($articles->get() as $article) {
                    $tagIds = $article->tags()->pluck('tags.id')->toArray();
                    if (!empty($tagIds)) {
                        Tag::whereIn('id', $tagIds)->decrement('usage_count');
                    }
                }
                
                $articles->delete();
                $message = 'Articles deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}

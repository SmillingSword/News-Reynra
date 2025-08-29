<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class GameController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the games.
     */
    public function index(Request $request)
    {
        // Temporary: Allow any authenticated user to access admin
        // $this->authorize('view games');

        $query = Game::withCount('articles')
            ->orderBy('title');

        // Search
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filter by genre
        if ($request->filled('genre')) {
            $query->whereJsonContains('genres', $request->genre);
        }

        // Filter by platform
        if ($request->filled('platform')) {
            $query->whereJsonContains('platforms', $request->platform);
        }

        $games = $query->paginate(20)->withQueryString();

        // Get available genres and platforms for filters
        $allGenres = Game::select('genres')->get()
            ->pluck('genres')
            ->flatten()
            ->unique()
            ->sort()
            ->values();
        $platforms = Game::select('platforms')->get()
            ->pluck('platforms')
            ->flatten()
            ->unique()
            ->sort()
            ->values();

        return Inertia::render('Admin/Games/Index', [
            'games' => $games,
            'filters' => $request->only(['search', 'genre', 'platform']),
            'genres' => $allGenres,
            'platforms' => $platforms,
        ]);
    }

    /**
     * Show the form for creating a new game.
     */
    public function create()
    {
        // $this->authorize('create games');

        return Inertia::render('Admin/Games/Create');
    }

    /**
     * Store a newly created game in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create games');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genres' => 'required|array|min:1',
            'genres.*' => 'string|max:100',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|max:50',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'esrb_rating' => 'nullable|string|max:10',
            'metacritic_score' => 'nullable|numeric|min:0|max:100',
            'cover_image' => 'nullable|string|max:500',
            'trailers' => 'nullable|array',
            'trailers.*' => 'url|max:500',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'url|max:500',
            'official_website' => 'nullable|url|max:500',
            'social_links' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Generate slug from title
        $slug = Str::slug($validated['title']);
        $originalSlug = $slug;
        $counter = 1;
        
        while (Game::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        Game::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'genres' => $validated['genres'],
            'platforms' => $validated['platforms'],
            'release_date' => $validated['release_date'],
            'developer' => $validated['developer'],
            'publisher' => $validated['publisher'],
            'esrb_rating' => $validated['esrb_rating'],
            'metacritic_score' => $validated['metacritic_score'],
            'cover_image' => $validated['cover_image'],
            'trailers' => $validated['trailers'],
            'screenshots' => $validated['screenshots'],
            'official_website' => $validated['official_website'],
            'social_links' => $validated['social_links'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'meta' => [
                'title' => $validated['meta_title'],
                'description' => $validated['meta_description'],
            ],
        ]);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game created successfully.');
    }

    /**
     * Display the specified game.
     */
    public function show(Game $game)
    {
        // $this->authorize('view games');

        $game->load(['articles' => function ($query) {
            $query->with('author')->orderBy('articles.created_at', 'desc')->limit(10);
        }]);

        return Inertia::render('Admin/Games/Show', [
            'game' => $game,
            'stats' => [
                'articles_count' => $game->articles()->count(),
                'published_articles' => $game->articles()->where('status', 'published')->count(),
                'average_rating' => $game->metacritic_score,
            ],
        ]);
    }

    /**
     * Show the form for editing the specified game.
     */
    public function edit(Game $game)
    {
        // $this->authorize('edit games');

        return Inertia::render('Admin/Games/Edit', [
            'game' => $game,
        ]);
    }

    /**
     * Update the specified game in storage.
     */
    public function update(Request $request, Game $game)
    {
        // $this->authorize('edit games');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'genres' => 'required|array|min:1',
            'genres.*' => 'string|max:100',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|max:50',
            'release_date' => 'nullable|date',
            'developer' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'esrb_rating' => 'nullable|string|max:10',
            'metacritic_score' => 'nullable|numeric|min:0|max:100',
            'cover_image' => 'nullable|string|max:500',
            'trailers' => 'nullable|array',
            'trailers.*' => 'url|max:500',
            'screenshots' => 'nullable|array',
            'screenshots.*' => 'url|max:500',
            'official_website' => 'nullable|url|max:500',
            'social_links' => 'nullable|array',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
        ]);

        // Update slug if title changed
        if ($validated['title'] !== $game->title) {
            $slug = Str::slug($validated['title']);
            $originalSlug = $slug;
            $counter = 1;
            
            while (Game::where('slug', $slug)->where('id', '!=', $game->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        }

        $game->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?? $game->slug,
            'description' => $validated['description'],
            'genres' => $validated['genres'],
            'platforms' => $validated['platforms'],
            'release_date' => $validated['release_date'],
            'developer' => $validated['developer'],
            'publisher' => $validated['publisher'],
            'esrb_rating' => $validated['esrb_rating'],
            'metacritic_score' => $validated['metacritic_score'],
            'cover_image' => $validated['cover_image'],
            'trailers' => $validated['trailers'],
            'screenshots' => $validated['screenshots'],
            'official_website' => $validated['official_website'],
            'social_links' => $validated['social_links'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'meta' => [
                'title' => $validated['meta_title'],
                'description' => $validated['meta_description'],
            ],
        ]);

        return redirect()->route('admin.games.index')
            ->with('success', 'Game updated successfully.');
    }

    /**
     * Remove the specified game from storage.
     */
    public function destroy(Game $game)
    {
        // $this->authorize('delete games');

        // Check if game has articles
        if ($game->articles()->count() > 0) {
            return back()->withErrors(['game' => 'Cannot delete game with articles. Please move or delete articles first.']);
        }

        $game->delete();

        return redirect()->route('admin.games.index')
            ->with('success', 'Game deleted successfully.');
    }

    /**
     * Toggle game featured status.
     */
    public function toggleFeatured(Game $game)
    {
        // $this->authorize('edit games');

        $game->update(['is_featured' => !$game->is_featured]);

        $status = $game->is_featured ? 'featured' : 'unfeatured';
        
        return back()->with('success', "Game {$status} successfully.");
    }

    /**
     * Toggle game active status.
     */
    public function toggleActive(Game $game)
    {
        // $this->authorize('edit games');

        $game->update(['is_active' => !$game->is_active]);

        $status = $game->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Game {$status} successfully.");
    }

    /**
     * Get featured games.
     */
    public function featured()
    {
        // $this->authorize('view games');

        $games = Game::where('is_featured', true)
            ->where('is_active', true)
            ->withCount('articles')
            ->orderBy('title')
            ->get();

        return response()->json($games);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Game;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicController extends Controller
{
    /**
     * Display the home page
     */
    public function home()
    {
        // Get featured articles
        $featuredArticles = Article::with(['categories', 'author'])
            ->where('status', 'published')
            ->where('is_featured', true)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        // Get trending articles (mock data - you can implement actual trending logic)
        $trendingArticles = Article::with(['categories', 'author'])
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->limit(8)
            ->get();

        // Get latest articles
        $latestArticles = Article::with(['categories', 'author'])
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Get categories with article counts
        $categories = Category::withCount('articles')
            ->orderBy('articles_count', 'desc')
            ->limit(8)
            ->get();

        return Inertia::render('Home', [
            'featuredArticles' => $featuredArticles,
            'trendingArticles' => $trendingArticles,
            'latestArticles' => $latestArticles,
            'categories' => $categories,
        ]);
    }

    /**
     * Display articles listing
     */
    public function articles(Request $request)
    {
        $query = Article::with(['categories', 'author', 'tags'])
            ->where('status', 'published');

        // Apply filters
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        if ($request->filled('game')) {
            $query->whereHas('games', function ($q) use ($request) {
                $q->where('slug', $request->game);
            });
        }

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('content', 'like', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'trending':
                $query->orderBy('views', 'desc');
                break;
            case 'popular':
                $query->orderBy('likes_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            default:
                $query->orderBy('published_at', 'desc');
        }

        $articles = $query->paginate(12);

        // Get categories for filter
        $categories = Category::orderBy('name')->get();

        // Determine page title and description
        $pageTitle = 'All Articles';
        $pageDescription = 'Browse all gaming news, reviews, and articles on News Reynra';

        if ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $pageTitle = $category->name . ' Articles';
                $pageDescription = "Browse {$category->name} articles on News Reynra";
            }
        }

        if ($request->filled('search')) {
            $pageTitle = "Search results for \"{$request->search}\"";
            $pageDescription = "Search results for \"{$request->search}\" on News Reynra";
        }

        return Inertia::render('Listing', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => $request->only(['category', 'tag', 'game', 'platform', 'search', 'sort']),
            'pageTitle' => $pageTitle,
            'pageDescription' => $pageDescription,
        ]);
    }

    /**
     * Display single article
     */
    public function article(Article $article, Request $request)
    {
        // Validasi artikel
        if (empty($article->content) || empty($article->title)) {
            abort(404, 'Artikel tidak memiliki konten yang valid');
        }

        if ($article->status !== 'published') {
            abort(404, 'Artikel tidak ditemukan atau belum dipublikasikan');
        }

        // Load relationships
        $article->load(['categories', 'author', 'tags', 'games']);

        // Fallback untuk featured_image
        if (empty($article->featured_image)) {
            $article->featured_image = '/images/default-article-image.jpg';
        }

        // Fallback untuk author avatar
        if ($article->author && empty($article->author->avatar)) {
            $article->author->avatar = '/images/default-avatar.png';
        }

        // Get related articles (articles that share categories)
        $categoryIds = $article->categories->pluck('id');
        $relatedArticles = Article::with(['categories', 'author'])
            ->where('status', 'published')
            ->where('id', '!=', $article->id)
            ->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            })
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        // Get approved comments with replies
        $comments = $article->comments()
            ->with(['user', 'replies.user'])
            ->where('status', 'approved')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->get();

        // Record article view using the new tracking system
        $article->recordView(
            $request->ip(),
            auth()->id(),
            $request->header('referer'),
            $request->header('user-agent')
        );

        return Inertia::render('Article', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
            'comments' => $comments,
        ]);
    }

    /**
     * Store a new comment
     */
    public function storeComment(Request $request, Article $article)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
            'author_name' => 'required_without:user_id|string|max:255',
            'author_email' => 'required_without:user_id|email|max:255',
        ]);

        $comment = $article->comments()->create([
            'content' => $validated['content'],
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => auth()->id(),
            'author_name' => auth()->check() ? null : $validated['author_name'],
            'author_email' => auth()->check() ? null : $validated['author_email'],
            'author_ip' => $request->ip(),
            'status' => 'approved', // Auto-approve for now, you can change this to 'pending'
        ]);

        return back()->with('success', 'Comment posted successfully!');
    }

    /**
     * Like a comment
     */
    public function likeComment(Request $request, $commentId)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return back()->withErrors(['error' => 'You must be logged in to like comments.']);
        }

        $comment = \App\Models\Comment::findOrFail($commentId);
        
        // Simple like increment (you can implement proper user-based likes later)
        $comment->increment('likes_count');

        // Return back to the article page with updated data
        return back()->with('success', 'Comment liked successfully!');
    }

    /**
     * Reply to a comment
     */
    public function replyComment(Request $request, $commentId)
    {
        $parentComment = \App\Models\Comment::findOrFail($commentId);
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'author_name' => 'required_without:user_id|string|max:255',
            'author_email' => 'required_without:user_id|email|max:255',
        ]);

        $reply = \App\Models\Comment::create([
            'content' => $validated['content'],
            'parent_id' => $parentComment->id,
            'article_id' => $parentComment->article_id,
            'user_id' => auth()->id(),
            'author_name' => auth()->check() ? null : $validated['author_name'],
            'author_email' => auth()->check() ? null : $validated['author_email'],
            'author_ip' => $request->ip(),
            'status' => 'approved',
        ]);

        return back()->with('success', 'Reply posted successfully!');
    }

    /**
     * Display games listing
     */
    public function games(Request $request)
    {
        $query = Game::query();

        // Apply filters
        if ($request->filled('genre')) {
            $query->whereJsonContains('genres', $request->genre);
        }

        if ($request->filled('platform')) {
            $query->whereJsonContains('platforms', $request->platform);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%")
                  ->orWhere('developer', 'like', "%{$searchTerm}%");
            });
        }

        // Apply sorting
        switch ($request->get('sort', 'title')) {
            case 'release_date':
                $query->orderBy('release_date', 'desc');
                break;
            case 'rating':
                $query->orderBy('metacritic_score', 'desc');
                break;
            default:
                $query->orderBy('title');
        }

        $games = $query->paginate(24);

        return Inertia::render('Games', [
            'games' => $games,
            'filters' => $request->only(['genre', 'platform', 'search', 'sort']),
        ]);
    }

    /**
     * Display single game
     */
    public function game(Game $game)
    {
        // Get articles related to this game
        $articles = Article::with(['category', 'author'])
            ->whereHas('games', function ($q) use ($game) {
                $q->where('games.id', $game->id);
            })
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        // Get similar games (same genres)
        $similarGames = collect();
        if ($game->genres) {
            $similarGames = Game::where('id', '!=', $game->id)
                ->where(function ($query) use ($game) {
                    foreach ($game->genres as $genre) {
                        $query->orWhereJsonContains('genres', $genre);
                    }
                })
                ->limit(12)
                ->get();
        }

        // Count articles for this game
        $articlesCount = Article::whereHas('games', function ($q) use ($game) {
            $q->where('games.id', $game->id);
        })->where('status', 'published')->count();

        return Inertia::render('Game', [
            'game' => $game,
            'articles' => $articles,
            'similarGames' => $similarGames,
            'articlesCount' => $articlesCount,
        ]);
    }

    /**
     * Display search results
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $results = [
            'articles' => collect(),
            'games' => collect(),
        ];

        if ($query) {
            // Search articles
            $articles = Article::with(['category', 'author'])
                ->where('status', 'published')
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                      ->orWhere('content', 'like', "%{$query}%")
                      ->orWhere('excerpt', 'like', "%{$query}%");
                })
                ->orderBy('published_at', 'desc')
                ->limit(20)
                ->get();

            // Search games
            $games = Game::where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('developer', 'like', "%{$query}%")
                  ->orWhere('publisher', 'like', "%{$query}%");
            })
            ->limit(12)
            ->get();

            $results = [
                'articles' => $articles,
                'games' => $games,
            ];
        }

        // Popular searches (mock data)
        $popularSearches = [
            'Cyberpunk 2077',
            'Elden Ring',
            'God of War',
            'FIFA 24',
            'Call of Duty',
            'Minecraft',
            'Valorant',
            'Genshin Impact'
        ];

        return Inertia::render('Search', [
            'query' => $query,
            'results' => $results,
            'popularSearches' => $popularSearches,
        ]);
    }

    /**
     * Display category page
     */
    public function category(Category $category)
    {
        $articles = Article::with(['category', 'author'])
            ->where('category_id', $category->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return Inertia::render('Listing', [
            'articles' => $articles,
            'categories' => Category::orderBy('name')->get(),
            'filters' => ['category' => $category->slug],
            'pageTitle' => $category->name . ' Articles',
            'pageDescription' => "Browse {$category->name} articles on News Reynra",
        ]);
    }

    /**
     * Display tag page
     */
    public function tag(Tag $tag)
    {
        $articles = Article::with(['category', 'author'])
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('tags.id', $tag->id);
            })
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return Inertia::render('Listing', [
            'articles' => $articles,
            'categories' => Category::orderBy('name')->get(),
            'filters' => ['tag' => $tag->slug],
            'pageTitle' => "Articles tagged with \"{$tag->name}\"",
            'pageDescription' => "Browse articles tagged with \"{$tag->name}\" on News Reynra",
        ]);
    }
}

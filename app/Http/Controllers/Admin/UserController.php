<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        // Temporary: Allow any authenticated user to access admin
        // $this->authorize('view users');

        $query = User::query()
            ->withCount(['articles', 'comments'])
            ->orderBy('created_at', 'desc');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'status']),
            'stats' => [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
                'admins' => User::whereHas('roles', function ($q) {
                    $q->where('name', 'admin');
                })->count(),
            ],
        ]);
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // $this->authorize('create users');

        return Inertia::render('Admin/Users/Create', [
            'roles' => \Spatie\Permission\Models\Role::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        // $this->authorize('create users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'email_verified' => 'boolean',
            'gaming_preferences' => 'array',
            'favorite_games' => 'array',
            'bio' => 'nullable|string|max:500',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => $validated['email_verified'] ?? false ? now() : null,
            'gaming_preferences' => $validated['gaming_preferences'] ?? null,
            'favorite_games' => $validated['favorite_games'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ]);

        // Assign roles
        if (!empty($validated['roles'])) {
            $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->pluck('name');
            $user->assignRole($roles);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // $this->authorize('view users');

        $user->load(['roles', 'permissions']);
        $user->loadCount(['articles', 'comments', 'bookmarks']);

        // Get recent activities
        $recentArticles = $user->articles()
            ->with('categories')
            ->latest()
            ->limit(5)
            ->get();

        $recentComments = $user->comments()
            ->with('article')
            ->latest()
            ->limit(5)
            ->get();

        return Inertia::render('Admin/Users/Show', [
            'user' => $user,
            'recentArticles' => $recentArticles,
            'recentComments' => $recentComments,
            'stats' => [
                'articles_count' => $user->articles_count,
                'comments_count' => $user->comments_count,
                'bookmarks_count' => $user->bookmarks_count,
                'join_date' => $user->created_at->format('M d, Y'),
                'last_login' => $user->updated_at->format('M d, Y H:i'),
            ],
        ]);
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // $this->authorize('edit users');

        $user->load('roles');

        return Inertia::render('Admin/Users/Edit', [
            'user' => $user,
            'roles' => \Spatie\Permission\Models\Role::all(['id', 'name']),
            'userRoles' => $user->roles->pluck('id'),
        ]);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // $this->authorize('edit users');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'email_verified' => 'boolean',
            'gaming_preferences' => 'array',
            'favorite_games' => 'array',
            'bio' => 'nullable|string|max:500',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => $validated['email_verified'] ?? false ? now() : null,
            'gaming_preferences' => $validated['gaming_preferences'] ?? null,
            'favorite_games' => $validated['favorite_games'] ?? null,
            'bio' => $validated['bio'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        // Sync roles
        if (isset($validated['roles'])) {
            $roles = \Spatie\Permission\Models\Role::whereIn('id', $validated['roles'])->pluck('name');
            $user->syncRoles($roles);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete users');

        // Prevent deleting the current user
        if ($user->id === Auth::id()) {
            return back()->withErrors(['user' => 'You cannot delete your own account.']);
        }

        // Check if user has articles
        if ($user->articles()->count() > 0) {
            return back()->withErrors(['user' => 'Cannot delete user with articles. Please reassign or delete articles first.']);
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user verification status.
     */
    public function toggleVerification(User $user)
    {
        $this->authorize('edit users');

        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);

        $status = $user->email_verified_at ? 'verified' : 'unverified';
        
        return back()->with('success', "User {$status} successfully.");
    }

    /**
     * Bulk actions for users.
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('edit users');

        $validated = $request->validate([
            'action' => 'required|in:verify,unverify,delete',
            'users' => 'required|array',
            'users.*' => 'exists:users,id',
        ]);

        $users = User::whereIn('id', $validated['users'])->get();

        switch ($validated['action']) {
            case 'verify':
                $users->each(function ($user) {
                    $user->update(['email_verified_at' => now()]);
                });
                $message = 'Users verified successfully.';
                break;

            case 'unverify':
                $users->each(function ($user) {
                    $user->update(['email_verified_at' => null]);
                });
                $message = 'Users unverified successfully.';
                break;

            case 'delete':
                // Prevent deleting current user
                $users = $users->reject(function ($user) {
                    return $user->id === Auth::id();
                });
                
                $users->each(function ($user) {
                    if ($user->articles()->count() === 0) {
                        $user->delete();
                    }
                });
                $message = 'Users deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Article permissions
            'view articles',
            'create articles',
            'edit articles',
            'delete articles',
            'publish articles',
            'schedule articles',
            'feature articles',
            
            // Category permissions
            'view categories',
            'create categories',
            'edit categories',
            'delete categories',
            
            // Tag permissions
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            
            // Game permissions
            'view games',
            'create games',
            'edit games',
            'delete games',
            
            // Author permissions
            'view authors',
            'create authors',
            'edit authors',
            'delete authors',
            
            // Comment permissions
            'view comments',
            'moderate comments',
            'delete comments',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage roles',
            
            // Media permissions
            'upload media',
            'delete media',
            'manage media library',
            
            // System permissions
            'view dashboard',
            'manage settings',
            'view analytics',
            'manage redirects',
            'manage news sources',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign permissions
        // Super Admin - has all permissions
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - has most permissions except user management
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $admin->givePermissionTo([
            'view articles', 'create articles', 'edit articles', 'delete articles', 
            'publish articles', 'schedule articles', 'feature articles',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view tags', 'create tags', 'edit tags', 'delete tags',
            'view games', 'create games', 'edit games', 'delete games',
            'view authors', 'create authors', 'edit authors',
            'view comments', 'moderate comments', 'delete comments',
            'upload media', 'delete media', 'manage media library',
            'view dashboard', 'view analytics', 'manage redirects', 'manage news sources',
        ]);

        // Editor - can manage content but not system settings
        $editor = Role::firstOrCreate(['name' => 'Editor']);
        $editor->givePermissionTo([
            'view articles', 'create articles', 'edit articles', 'publish articles', 'schedule articles',
            'view categories', 'create categories', 'edit categories',
            'view tags', 'create tags', 'edit tags',
            'view games', 'create games', 'edit games',
            'view authors',
            'view comments', 'moderate comments',
            'upload media', 'manage media library',
            'view dashboard', 'view analytics',
        ]);

        // Author - can create and edit their own articles
        $author = Role::firstOrCreate(['name' => 'Author']);
        $author->givePermissionTo([
            'view articles', 'create articles', 'edit articles',
            'view categories', 'view tags', 'view games',
            'upload media',
            'view dashboard',
        ]);

        // Proofreader - can review and edit articles but not publish
        $proofreader = Role::firstOrCreate(['name' => 'Proofreader']);
        $proofreader->givePermissionTo([
            'view articles', 'edit articles',
            'view categories', 'view tags', 'view games', 'view authors',
            'view dashboard',
        ]);

        // Moderator - focuses on community management
        $moderator = Role::firstOrCreate(['name' => 'Moderator']);
        $moderator->givePermissionTo([
            'view articles',
            'view comments', 'moderate comments', 'delete comments',
            'view users',
            'view dashboard',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}

<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run the seeders in order
        $this->call([
            RoleAndPermissionSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            GamingNewsSourceSeeder::class, // Add gaming news sources
            NewsSourceFixSeeder::class, // Fix existing news sources
        ]);

        // Create a test admin user
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@newsreynra.com'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'is_verified' => true,
                'verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        // Assign Super Admin role to the admin user
        if (!$adminUser->hasRole('Super Admin')) {
            $adminUser->assignRole('Super Admin');
        }

        // Create a test editor user
        $editorUser = User::firstOrCreate(
            ['email' => 'editor@newsreynra.com'],
            [
                'name' => 'Editor User',
                'username' => 'editor',
                'is_verified' => true,
                'verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        // Assign Editor role to the editor user
        if (!$editorUser->hasRole('Editor')) {
            $editorUser->assignRole('Editor');
        }

        // Create a test author user
        $authorUser = User::firstOrCreate(
            ['email' => 'author@newsreynra.com'],
            [
                'name' => 'Author User',
                'username' => 'author',
                'is_verified' => true,
                'verified_at' => now(),
                'password' => bcrypt('password'),
            ]
        );

        // Assign Author role to the author user
        if (!$authorUser->hasRole('Author')) {
            $authorUser->assignRole('Author');
        }

        // Create some regular users
        User::factory(10)->create();

        // Seed articles after users are created
        $this->call([
            ArticleSeeder::class,
            CommentSeeder::class,
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Admin credentials: admin@newsreynra.com / password');
        $this->command->info('Editor credentials: editor@newsreynra.com / password');
        $this->command->info('Author credentials: author@newsreynra.com / password');
    }
}

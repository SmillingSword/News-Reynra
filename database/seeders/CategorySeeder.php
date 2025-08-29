<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            // Main Platform Categories
            [
                'name' => 'PC Gaming',
                'description' => 'News and reviews for PC games, hardware, and gaming culture',
                'color' => '#3B82F6',
                'icon' => 'desktop-computer',
                'sort_order' => 1,
                'meta' => [
                    'title' => 'PC Gaming News & Reviews',
                    'description' => 'Latest PC gaming news, reviews, and hardware updates',
                ],
            ],
            [
                'name' => 'Console Gaming',
                'description' => 'PlayStation, Xbox, Nintendo Switch and other console news',
                'color' => '#10B981',
                'icon' => 'device-gamepad',
                'sort_order' => 2,
                'meta' => [
                    'title' => 'Console Gaming News',
                    'description' => 'Latest console gaming news for PlayStation, Xbox, Nintendo Switch',
                ],
            ],
            [
                'name' => 'Mobile Gaming',
                'description' => 'iOS and Android gaming news, reviews, and guides',
                'color' => '#F59E0B',
                'icon' => 'device-mobile',
                'sort_order' => 3,
                'meta' => [
                    'title' => 'Mobile Gaming News',
                    'description' => 'Latest mobile gaming news for iOS and Android platforms',
                ],
            ],
            [
                'name' => 'Esports',
                'description' => 'Competitive gaming, tournaments, and professional gaming news',
                'color' => '#EF4444',
                'icon' => 'trophy',
                'sort_order' => 4,
                'meta' => [
                    'title' => 'Esports News & Tournaments',
                    'description' => 'Latest esports news, tournament results, and competitive gaming updates',
                ],
            ],
            [
                'name' => 'Indie Games',
                'description' => 'Independent game development and indie game spotlights',
                'color' => '#8B5CF6',
                'icon' => 'heart',
                'sort_order' => 5,
                'meta' => [
                    'title' => 'Indie Games News',
                    'description' => 'Discover the latest indie games and independent developers',
                ],
            ],

            // Content Type Categories
            [
                'name' => 'Reviews',
                'description' => 'In-depth game reviews and ratings',
                'color' => '#06B6D4',
                'icon' => 'star',
                'sort_order' => 6,
                'meta' => [
                    'title' => 'Game Reviews',
                    'description' => 'Professional game reviews and ratings from our expert team',
                ],
            ],
            [
                'name' => 'Guides',
                'description' => 'Gaming guides, tutorials, and how-to articles',
                'color' => '#84CC16',
                'icon' => 'book-open',
                'sort_order' => 7,
                'meta' => [
                    'title' => 'Gaming Guides & Tutorials',
                    'description' => 'Comprehensive gaming guides and tutorials for all skill levels',
                ],
            ],
            [
                'name' => 'Industry News',
                'description' => 'Gaming industry updates, business news, and market trends',
                'color' => '#6B7280',
                'icon' => 'briefcase',
                'sort_order' => 8,
                'meta' => [
                    'title' => 'Gaming Industry News',
                    'description' => 'Latest gaming industry news, business updates, and market analysis',
                ],
            ],
            [
                'name' => 'Hardware',
                'description' => 'Gaming hardware reviews, news, and recommendations',
                'color' => '#374151',
                'icon' => 'cpu-chip',
                'sort_order' => 9,
                'meta' => [
                    'title' => 'Gaming Hardware News',
                    'description' => 'Latest gaming hardware reviews, news, and buying guides',
                ],
            ],
            [
                'name' => 'Deals',
                'description' => 'Game deals, sales, and promotional offers',
                'color' => '#F97316',
                'icon' => 'tag',
                'sort_order' => 10,
                'meta' => [
                    'title' => 'Gaming Deals & Sales',
                    'description' => 'Best gaming deals, sales, and promotional offers',
                ],
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create some subcategories
        $pcGaming = Category::where('name', 'PC Gaming')->first();
        $consoleGaming = Category::where('name', 'Console Gaming')->first();

        // PC Gaming subcategories
        if ($pcGaming) {
            Category::create([
                'name' => 'Steam',
                'parent_id' => $pcGaming->id,
                'description' => 'Steam platform news and game releases',
                'color' => '#1B2838',
                'icon' => 'steam',
                'sort_order' => 1,
            ]);

            Category::create([
                'name' => 'Epic Games Store',
                'parent_id' => $pcGaming->id,
                'description' => 'Epic Games Store news and free games',
                'color' => '#313131',
                'icon' => 'epic-games',
                'sort_order' => 2,
            ]);
        }

        // Console Gaming subcategories
        if ($consoleGaming) {
            Category::create([
                'name' => 'PlayStation',
                'parent_id' => $consoleGaming->id,
                'description' => 'PlayStation console news and exclusives',
                'color' => '#003791',
                'icon' => 'playstation',
                'sort_order' => 1,
            ]);

            Category::create([
                'name' => 'Xbox',
                'parent_id' => $consoleGaming->id,
                'description' => 'Xbox console news and Game Pass updates',
                'color' => '#107C10',
                'icon' => 'xbox',
                'sort_order' => 2,
            ]);

            Category::create([
                'name' => 'Nintendo Switch',
                'parent_id' => $consoleGaming->id,
                'description' => 'Nintendo Switch games and news',
                'color' => '#E60012',
                'icon' => 'nintendo-switch',
                'sort_order' => 3,
            ]);
        }

        $this->command->info('Categories created successfully!');
    }
}

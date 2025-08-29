<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            // Game Genres
            ['name' => 'Action', 'description' => 'Fast-paced games with combat and challenges', 'color' => '#EF4444'],
            ['name' => 'Adventure', 'description' => 'Story-driven exploration games', 'color' => '#10B981'],
            ['name' => 'RPG', 'description' => 'Role-playing games with character progression', 'color' => '#8B5CF6'],
            ['name' => 'Strategy', 'description' => 'Tactical and strategic gameplay', 'color' => '#3B82F6'],
            ['name' => 'Simulation', 'description' => 'Life and world simulation games', 'color' => '#06B6D4'],
            ['name' => 'Sports', 'description' => 'Athletic and sports-themed games', 'color' => '#F59E0B'],
            ['name' => 'Racing', 'description' => 'Vehicle racing and driving games', 'color' => '#EF4444'],
            ['name' => 'Puzzle', 'description' => 'Brain-teasing and logic games', 'color' => '#84CC16'],
            ['name' => 'Horror', 'description' => 'Scary and suspenseful games', 'color' => '#1F2937'],
            ['name' => 'Shooter', 'description' => 'First and third-person shooting games', 'color' => '#DC2626'],
            ['name' => 'MMORPG', 'description' => 'Massively multiplayer online role-playing games', 'color' => '#7C3AED'],
            ['name' => 'Battle Royale', 'description' => 'Last-player-standing competitive games', 'color' => '#F97316'],
            ['name' => 'MOBA', 'description' => 'Multiplayer online battle arena games', 'color' => '#059669'],
            ['name' => 'Platformer', 'description' => 'Jump and run platform games', 'color' => '#0891B2'],
            ['name' => 'Fighting', 'description' => 'Combat and martial arts games', 'color' => '#BE123C'],

            // Platforms
            ['name' => 'Steam', 'description' => 'Steam platform games and news', 'color' => '#1B2838'],
            ['name' => 'PlayStation 5', 'description' => 'Sony PlayStation 5 console', 'color' => '#003791'],
            ['name' => 'Xbox Series X', 'description' => 'Microsoft Xbox Series X console', 'color' => '#107C10'],
            ['name' => 'Nintendo Switch', 'description' => 'Nintendo Switch console', 'color' => '#E60012'],
            ['name' => 'Epic Games Store', 'description' => 'Epic Games Store platform', 'color' => '#313131'],
            ['name' => 'Game Pass', 'description' => 'Xbox Game Pass subscription service', 'color' => '#107C10'],
            ['name' => 'PlayStation Plus', 'description' => 'PlayStation Plus subscription service', 'color' => '#003791'],

            // Gaming Topics
            ['name' => 'Early Access', 'description' => 'Games in early access development', 'color' => '#F59E0B'],
            ['name' => 'Beta', 'description' => 'Beta testing and preview builds', 'color' => '#6366F1'],
            ['name' => 'DLC', 'description' => 'Downloadable content and expansions', 'color' => '#8B5CF6'],
            ['name' => 'Patch', 'description' => 'Game updates and patches', 'color' => '#10B981'],
            ['name' => 'Mod', 'description' => 'Game modifications and mods', 'color' => '#F97316'],
            ['name' => 'Speedrun', 'description' => 'Speedrunning and competitive completion', 'color' => '#EF4444'],
            ['name' => 'Streaming', 'description' => 'Game streaming and content creation', 'color' => '#8B5CF6'],
            ['name' => 'VR', 'description' => 'Virtual reality gaming', 'color' => '#06B6D4'],
            ['name' => 'AR', 'description' => 'Augmented reality gaming', 'color' => '#0891B2'],
            ['name' => 'Cross-platform', 'description' => 'Cross-platform gaming and compatibility', 'color' => '#059669'],

            // Events and Releases
            ['name' => 'E3', 'description' => 'Electronic Entertainment Expo', 'color' => '#DC2626'],
            ['name' => 'Gamescom', 'description' => 'Gamescom gaming convention', 'color' => '#7C3AED'],
            ['name' => 'PAX', 'description' => 'Penny Arcade Expo gaming events', 'color' => '#059669'],
            ['name' => 'Game Awards', 'description' => 'The Game Awards ceremony', 'color' => '#F59E0B'],
            ['name' => 'Release Date', 'description' => 'Game release announcements', 'color' => '#10B981'],
            ['name' => 'Delay', 'description' => 'Game release delays', 'color' => '#EF4444'],
            ['name' => 'Announcement', 'description' => 'New game announcements', 'color' => '#3B82F6'],

            // Companies and Developers
            ['name' => 'Nintendo', 'description' => 'Nintendo games and news', 'color' => '#E60012'],
            ['name' => 'Sony', 'description' => 'Sony PlayStation news', 'color' => '#003791'],
            ['name' => 'Microsoft', 'description' => 'Microsoft Xbox news', 'color' => '#107C10'],
            ['name' => 'Valve', 'description' => 'Valve Corporation news', 'color' => '#1B2838'],
            ['name' => 'Epic Games', 'description' => 'Epic Games news and updates', 'color' => '#313131'],
            ['name' => 'Activision Blizzard', 'description' => 'Activision Blizzard games', 'color' => '#F97316'],
            ['name' => 'EA', 'description' => 'Electronic Arts games', 'color' => '#0066CC'],
            ['name' => 'Ubisoft', 'description' => 'Ubisoft games and news', 'color' => '#0099FF'],

            // Trending Topics
            ['name' => 'Trending', 'description' => 'Currently trending gaming topics', 'color' => '#EF4444'],
            ['name' => 'Viral', 'description' => 'Viral gaming content and memes', 'color' => '#F59E0B'],
            ['name' => 'Controversy', 'description' => 'Gaming controversies and debates', 'color' => '#DC2626'],
            ['name' => 'Award Winner', 'description' => 'Award-winning games', 'color' => '#F59E0B'],
            ['name' => 'Bestseller', 'description' => 'Best-selling games', 'color' => '#10B981'],
        ];

        foreach ($tags as $tagData) {
            Tag::create($tagData);
        }

        $this->command->info('Tags created successfully!');
    }
}

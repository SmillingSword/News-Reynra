<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $articles = Article::where('status', 'published')->get();
        $users = User::all();

        foreach ($articles as $article) {
            // Create 3-5 comments per article
            $commentCount = rand(3, 5);
            
            for ($i = 0; $i < $commentCount; $i++) {
                $isAnonymous = rand(0, 1); // 50% chance for anonymous comments
                
                $comment = Comment::create([
                    'article_id' => $article->id,
                    'user_id' => $isAnonymous ? null : $users->random()->id,
                    'content' => $this->getRandomComment(),
                    'author_name' => $isAnonymous ? $this->getRandomName() : null,
                    'author_email' => $isAnonymous ? $this->getRandomEmail() : null,
                    'author_ip' => $this->getRandomIP(),
                    'status' => 'approved',
                    'likes_count' => rand(0, 25),
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);

                // Create 1-2 replies for some comments
                if (rand(0, 1)) {
                    $replyCount = rand(1, 2);
                    
                    for ($j = 0; $j < $replyCount; $j++) {
                        $isAnonymousReply = rand(0, 1);
                        
                        Comment::create([
                            'article_id' => $article->id,
                            'parent_id' => $comment->id,
                            'user_id' => $isAnonymousReply ? null : $users->random()->id,
                            'content' => $this->getRandomReply(),
                            'author_name' => $isAnonymousReply ? $this->getRandomName() : null,
                            'author_email' => $isAnonymousReply ? $this->getRandomEmail() : null,
                            'author_ip' => $this->getRandomIP(),
                            'status' => 'approved',
                            'likes_count' => rand(0, 10),
                            'created_at' => $comment->created_at->addMinutes(rand(10, 120)),
                        ]);
                    }
                }
            }
        }

        $this->command->info('Comments created successfully!');
    }

    private function getRandomComment(): string
    {
        $comments = [
            'Great article! Really enjoyed reading this.',
            'This is exactly what I was looking for. Thanks for sharing!',
            'Interesting perspective on this topic.',
            'I disagree with some points, but overall a good read.',
            'Can you provide more details about this?',
            'This helped me understand the topic better.',
            'Looking forward to more articles like this.',
            'Well written and informative!',
            'I had a similar experience with this game.',
            'The graphics in this game are absolutely stunning!',
            'This review convinced me to try the game.',
            'I\'ve been waiting for this game for so long!',
            'The gameplay mechanics sound really interesting.',
            'How does this compare to the previous version?',
            'Thanks for the detailed review!',
            'This game looks amazing, definitely adding to my wishlist.',
            'I love the art style of this game.',
            'The story sounds really compelling.',
            'Great analysis of the game mechanics.',
            'This is one of my favorite games of all time!',
        ];

        return $comments[array_rand($comments)];
    }

    private function getRandomReply(): string
    {
        $replies = [
            'I totally agree with you!',
            'Thanks for your comment!',
            'That\'s a good point.',
            'I see what you mean.',
            'You should definitely try it!',
            'I had the same thought.',
            'Glad you found it helpful!',
            'Let me know what you think!',
            'I\'m curious about your experience too.',
            'That\'s exactly right!',
            'Thanks for the recommendation!',
            'I\'ll have to check that out.',
            'Good question, I wonder about that too.',
            'You make a valid point.',
            'I appreciate your feedback!',
        ];

        return $replies[array_rand($replies)];
    }

    private function getRandomName(): string
    {
        $names = [
            'Alex Johnson', 'Sarah Wilson', 'Mike Chen', 'Emma Davis',
            'John Smith', 'Lisa Brown', 'David Lee', 'Anna Garcia',
            'Chris Taylor', 'Maria Rodriguez', 'James Miller', 'Jessica White',
            'Ryan Anderson', 'Ashley Martinez', 'Kevin Thompson', 'Amanda Clark',
        ];

        return $names[array_rand($names)];
    }

    private function getRandomEmail(): string
    {
        $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
        $username = strtolower(str_replace(' ', '.', $this->getRandomName()));
        $domain = $domains[array_rand($domains)];
        
        return $username . '@' . $domain;
    }

    private function getRandomIP(): string
    {
        return rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
    }
}

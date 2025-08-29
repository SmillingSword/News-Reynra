<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\User;
use App\Models\Author;
use Illuminate\Support\Str;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        // Get or create categories
        $reviewCategory = Category::firstOrCreate(
            ['slug' => 'review'],
            ['name' => 'Review', 'description' => 'Game reviews and ratings', 'color' => '#10b981']
        );
        $esportsCategory = Category::firstOrCreate(
            ['slug' => 'esports'],
            ['name' => 'Esports', 'description' => 'Competitive gaming news', 'color' => '#f59e0b']
        );
        $newsCategory = Category::firstOrCreate(
            ['slug' => 'news'],
            ['name' => 'News', 'description' => 'Gaming industry news', 'color' => '#3b82f6']
        );
        $guideCategory = Category::firstOrCreate(
            ['slug' => 'guide'],
            ['name' => 'Guide', 'description' => 'Gaming guides and tutorials', 'color' => '#8b5cf6']
        );
        
        // Get or create admin user
        $adminUser = User::where('email', 'admin@newsreynra.com')->first();
        if (!$adminUser) {
            $adminUser = User::factory()->create([
                'name' => 'Admin User',
                'username' => 'admin',
                'email' => 'admin@newsreynra.com',
                'is_verified' => true,
                'verified_at' => now(),
            ]);
        }
        
        // Create or get authors
        $author1 = Author::firstOrCreate(
            ['user_id' => $adminUser->id],
            [
                'display_name' => 'Reynra Gaming',
                'bio' => 'Senior gaming journalist dengan pengalaman 8+ tahun di industri gaming. Spesialis review game AAA dan indie.',
                'specialties' => ['Game Reviews', 'Industry Analysis', 'Hardware Testing'],
                'social_links' => [
                    'twitter' => 'https://twitter.com/reynragaming',
                    'instagram' => 'https://instagram.com/reynragaming'
                ],
                'location' => 'Jakarta, Indonesia',
                'is_active' => true,
                'is_featured' => true,
                'articles_count' => 5,
                'last_article_at' => now(),
                'meta' => [
                    'title' => 'Reynra Gaming - Senior Gaming Journalist',
                    'description' => 'Senior gaming journalist dengan spesialisasi review game AAA dan indie, serta analisis industri gaming.',
                    'keywords' => 'gaming journalist, game reviews, reynra gaming'
                ]
            ]
        );
        
        // Get or create tags
        $tags = [
            'action' => Tag::firstOrCreate(['name' => 'Action', 'slug' => 'action']),
            'rpg' => Tag::firstOrCreate(['name' => 'RPG', 'slug' => 'rpg']),
            'fps' => Tag::firstOrCreate(['name' => 'FPS', 'slug' => 'fps']),
            'moba' => Tag::firstOrCreate(['name' => 'MOBA', 'slug' => 'moba']),
            'battle-royale' => Tag::firstOrCreate(['name' => 'Battle Royale', 'slug' => 'battle-royale']),
            'indie' => Tag::firstOrCreate(['name' => 'Indie', 'slug' => 'indie']),
            'multiplayer' => Tag::firstOrCreate(['name' => 'Multiplayer', 'slug' => 'multiplayer']),
            'single-player' => Tag::firstOrCreate(['name' => 'Single Player', 'slug' => 'single-player']),
            'platformer' => Tag::firstOrCreate(['name' => 'Platformer', 'slug' => 'platformer']),
            'strategy' => Tag::firstOrCreate(['name' => 'Strategy', 'slug' => 'strategy']),
        ];

        // Article 1: Review Game AAA
        $article1 = Article::create([
            'title' => 'Review Cyberpunk 2077: Phantom Liberty - Ekspansi yang Menyelamatkan Reputasi',
            'slug' => 'review-cyberpunk-2077-phantom-liberty-ekspansi-menyelamatkan-reputasi',
            'subtitle' => 'DLC Phantom Liberty membawa Cyberpunk 2077 ke level yang seharusnya sejak awal',
            'excerpt' => 'Setelah peluncuran yang kontroversial, CD Projekt RED akhirnya menunjukkan potensi sebenarnya dari Cyberpunk 2077 melalui ekspansi Phantom Liberty yang memukau.',
            'content' => '<h2>Kembalinya Night City yang Memukau</h2>

<p>Tiga tahun setelah peluncuran yang penuh kontroversi, Cyberpunk 2077 akhirnya menunjukkan wajah terbaiknya melalui ekspansi Phantom Liberty. DLC ini bukan hanya sekadar tambahan konten, melainkan bukti nyata bahwa CD Projekt RED telah belajar dari kesalahan masa lalu.</p>

<h3>Cerita yang Mencengkeram</h3>

<p>Phantom Liberty menghadirkan narasi mata-mata yang kompleks dengan Keanu Reeves kembali memerankan Johnny Silverhand. Kali ini, V terlibat dalam misi penyelamatan Presiden NUSA yang jatuh di Night City. Alur cerita yang penuh tikungan tajam dan dilema moral membuat setiap keputusan terasa berat dan bermakna.</p>

<blockquote>
"Phantom Liberty membuktikan bahwa Cyberpunk 2077 selalu memiliki potensi untuk menjadi masterpiece, hanya saja butuh waktu untuk merealisasikannya."
</blockquote>

<h3>Gameplay yang Dipoles</h3>

<p>Sistem pertarungan telah mengalami overhaul besar-besaran. Cybernetic abilities kini terasa lebih responsif, sementara AI musuh jauh lebih cerdas dan menantang. Stealth gameplay yang sebelumnya terasa janggal kini menjadi pilihan yang viable dan menyenangkan.</p>

<h3>Visual dan Performa</h3>

<p>Night City kini benar-benar hidup. Ray tracing yang dioptimalkan menghadirkan refleksi dan pencahayaan yang memukau, sementara DLSS 3 memastikan performa yang stabil bahkan di pengaturan tertinggi. Bug-bug yang mengganggu di versi awal kini sudah hampir sepenuhnya teratasi.</p>

<h3>Kesimpulan</h3>

<p>Phantom Liberty adalah penebusan dosa CD Projekt RED. Ekspansi ini menunjukkan visi asli Cyberpunk 2077 yang akhirnya terealisasi dengan sempurna. Bagi yang pernah kecewa dengan game ini, sekarang adalah waktu yang tepat untuk kembali ke Night City.</p>

<p><strong>Rating: 9/10</strong></p>

<p><em>Phantom Liberty tersedia untuk PC, PlayStation 5, dan Xbox Series X/S dengan harga $29.99.</em></p>',
            'featured_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=600&fit=crop',
            'author_id' => $author1->id,
            'type' => 'review',
            'status' => 'published',
            'is_featured' => true,
            'reading_time' => 8,
            'views_count' => 15420,
            'rating' => 9.0,
            'pros' => [
                'Cerita yang mencengkeram dan penuh twist',
                'Gameplay yang sudah dipoles dengan baik',
                'Visual dan performa yang memukau',
                'Keanu Reeves memberikan performa terbaiknya',
                'Bug sudah hampir sepenuhnya teratasi'
            ],
            'cons' => [
                'Harga yang cukup mahal untuk DLC',
                'Masih ada beberapa bug minor',
                'Membutuhkan hardware yang mumpuni'
            ],
            'meta' => [
                'title' => 'Review Cyberpunk 2077: Phantom Liberty - Ekspansi Terbaik 2023',
                'description' => 'Review lengkap ekspansi Phantom Liberty untuk Cyberpunk 2077. Apakah DLC ini berhasil menyelamatkan reputasi game kontroversial CD Projekt RED?',
                'keywords' => 'cyberpunk 2077, phantom liberty, review, cd projekt red, keanu reeves'
            ],
            'social_meta' => [
                'og_title' => 'Review Cyberpunk 2077: Phantom Liberty - Game of the Year?',
                'og_description' => 'Ekspansi Phantom Liberty akhirnya menunjukkan potensi sebenarnya Cyberpunk 2077. Baca review lengkap kami!',
                'og_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=630&fit=crop',
                'twitter_title' => 'Cyberpunk 2077: Phantom Liberty Review',
                'twitter_description' => 'DLC yang menyelamatkan reputasi Cyberpunk 2077. Rating: 9/10',
                'twitter_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=630&fit=crop'
            ],
            'published_at' => now()->subDays(1),
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);
        $article1->categories()->attach([$reviewCategory->id]);
        $article1->tags()->attach([$tags['action']->id, $tags['rpg']->id, $tags['single-player']->id]);

        // Article 2: Esports News
        $article2 = Article::create([
            'title' => 'Tim Indonesia Juara Dunia Mobile Legends: Bang Bang M5 World Championship',
            'slug' => 'tim-indonesia-juara-dunia-mobile-legends-m5-world-championship',
            'subtitle' => 'RRQ Hoshi mengalahkan Fnatic ONIC PH di final yang dramatis',
            'excerpt' => 'Sejarah baru terukir! Tim Indonesia berhasil meraih gelar juara dunia Mobile Legends: Bang Bang M5 World Championship setelah pertarungan sengit selama 7 game.',
            'content' => '<h2>Momen Bersejarah Esports Indonesia</h2>

<p>Jakarta, 19 Januari 2025 - RRQ Hoshi berhasil mengukir sejarah dengan menjadi tim Indonesia pertama yang meraih gelar juara dunia Mobile Legends: Bang Bang M5 World Championship. Dalam final yang berlangsung di Axiata Arena, Kuala Lumpur, mereka mengalahkan Fnatic ONIC Philippines dengan skor 4-3 dalam pertarungan best-of-seven yang dramatis.</p>

<h3>Perjalanan Menuju Final</h3>

<p>RRQ Hoshi menunjukkan performa konsisten sepanjang turnamen. Dimulai dari fase grup dengan rekor sempurna 6-0, mereka melanjutkan dominasi di playoff dengan mengalahkan tim-tim kuat seperti Blacklist International dan ECHO.</p>

<h3>Final yang Mencekam</h3>

<p>Pertandingan final dimulai dengan keunggulan Fnatic ONIC PH yang memimpin 2-0. Namun, RRQ Hoshi menunjukkan mental juara dengan melakukan comeback spektakuler. Game ketujuh yang menentukan berlangsung selama 45 menit dengan pertukaran kill yang intens.</p>

<blockquote>
"Ini adalah momen yang sudah kami impikan sejak lama. Terima kasih untuk semua dukungan dari fans Indonesia," ucap Vyn, captain RRQ Hoshi, sambil menangis haru.
</blockquote>

<h3>MVP Tournament</h3>

<p>Albert "Alberttt" Iskandar terpilih sebagai MVP tournament berkat performa luar biasa sebagai jungler. Sepanjang turnamen, ia mencatat KDA rata-rata 8.2 dengan win rate 89% menggunakan hero-hero seperti Ling, Lancelot, dan Hayabusa.</p>

<h3>Hadiah dan Dampak</h3>

<p>Kemenangan ini membawa pulang hadiah $300,000 untuk RRQ Hoshi. Lebih dari itu, prestasi ini diharapkan dapat meningkatkan ekosistem esports Indonesia dan membuka lebih banyak peluang bagi talenta-talenta muda.</p>

<p>Presiden Joko Widodo turut memberikan selamat melalui akun media sosialnya, "Selamat untuk RRQ Hoshi! Kalian telah mengharumkan nama Indonesia di kancah esports dunia."</p>

<h3>Apa Selanjutnya?</h3>

<p>RRQ Hoshi kini akan fokus pada persiapan untuk season baru MPL Indonesia dan turnamen internasional lainnya. Dengan momentum kemenangan ini, mereka optimis dapat mempertahankan dominasi di tahun 2025.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1560253023-3ec5d502959f?w=1200&h=600&fit=crop',
            'author_id' => $author1->id,
            'type' => 'news',
            'status' => 'published',
            'is_featured' => true,
            'reading_time' => 6,
            'views_count' => 28750,
            'meta' => [
                'title' => 'RRQ Hoshi Juara Dunia Mobile Legends M5 World Championship',
                'description' => 'Tim Indonesia RRQ Hoshi berhasil meraih gelar juara dunia Mobile Legends M5 setelah mengalahkan Fnatic ONIC PH 4-3 di final.',
                'keywords' => 'rrq hoshi, mobile legends, m5 world championship, esports indonesia, juara dunia'
            ],
            'social_meta' => [
                'og_title' => 'SEJARAH! RRQ Hoshi Juara Dunia Mobile Legends M5',
                'og_description' => 'Tim Indonesia berhasil mengalahkan Fnatic ONIC PH 4-3 di final yang dramatis. Prestasi bersejarah untuk esports Indonesia!',
                'og_image' => 'https://images.unsplash.com/photo-1560253023-3ec5d502959f?w=1200&h=630&fit=crop',
                'twitter_title' => 'RRQ Hoshi Juara Dunia M5!',
                'twitter_description' => 'Sejarah terukir! Indonesia juara dunia Mobile Legends untuk pertama kalinya.',
                'twitter_image' => 'https://images.unsplash.com/photo-1560253023-3ec5d502959f?w=1200&h=630&fit=crop'
            ],
            'published_at' => now()->subHours(12),
            'created_at' => now()->subHours(12),
            'updated_at' => now()->subHours(12),
        ]);
        $article2->categories()->attach([$esportsCategory->id]);
        $article2->tags()->attach([$tags['moba']->id, $tags['multiplayer']->id]);

        // Article 3: Gaming News
        $article3 = Article::create([
            'title' => 'PlayStation 6 Dikonfirmasi Rilis 2028, Akan Dukung 8K Gaming dan Ray Tracing Real-Time',
            'slug' => 'playstation-6-dikonfirmasi-rilis-2028-dukung-8k-gaming-ray-tracing',
            'subtitle' => 'Sony mengumumkan konsol generasi berikutnya dengan teknologi revolusioner',
            'excerpt' => 'Sony Interactive Entertainment resmi mengkonfirmasi pengembangan PlayStation 6 yang dijadwalkan rilis pada 2028 dengan dukungan 8K gaming dan ray tracing real-time.',
            'content' => '<h2>Era Baru Gaming Console</h2>

<p>Tokyo, 18 Januari 2025 - Sony Interactive Entertainment hari ini secara resmi mengumumkan bahwa mereka sedang mengembangkan PlayStation 6, konsol gaming generasi berikutnya yang dijadwalkan akan dirilis pada tahun 2028. Pengumuman ini disampaikan oleh CEO Sony Interactive Entertainment, Jim Ryan, dalam acara CES 2025.</p>

<h3>Spesifikasi Revolusioner</h3>

<p>PlayStation 6 akan mengusung teknologi terdepan yang belum pernah ada sebelumnya:</p>

<ul>
<li><strong>Prosesor:</strong> Custom AMD Zen 5 dengan 16 core dan clock speed hingga 4.5GHz</li>
<li><strong>GPU:</strong> Custom RDNA 4 dengan 32GB GDDR7 RAM</li>
<li><strong>Storage:</strong> 2TB NVMe SSD dengan kecepatan baca 15GB/s</li>
<li><strong>Ray Tracing:</strong> Hardware-accelerated real-time ray tracing</li>
<li><strong>Resolusi:</strong> Native 8K gaming pada 60fps, 4K pada 120fps</li>
</ul>

<h3>Fitur Inovatif</h3>

<p>Selain peningkatan performa, PlayStation 6 akan menghadirkan beberapa fitur revolusioner:</p>

<h4>Neural Rendering</h4>
<p>Teknologi AI yang dapat meningkatkan kualitas grafis secara real-time, mengubah game lama menjadi tampak seperti game modern tanpa perlu remaster.</p>

<h4>Haptic Feedback 2.0</h4>
<p>Evolusi dari DualSense controller dengan feedback yang lebih presisi dan immersive, termasuk simulasi tekstur dan temperatur.</p>

<h4>Cloud-Native Gaming</h4>
<p>Integrasi penuh dengan PlayStation Cloud yang memungkinkan streaming game 8K tanpa lag, bahkan untuk game yang belum terinstall.</p>

<blockquote>
"PlayStation 6 akan mendefinisikan ulang apa yang mungkin dalam gaming. Kami tidak hanya meningkatkan performa, tapi menciptakan pengalaman yang benar-benar baru," kata Jim Ryan.
</blockquote>

<h3>Backward Compatibility</h3>

<p>Sony mengkonfirmasi bahwa PlayStation 6 akan mendukung backward compatibility penuh untuk game PS4 dan PS5, bahkan dengan peningkatan visual otomatis melalui teknologi neural rendering.</p>

<h3>Harga dan Ketersediaan</h3>

<p>Meskipun belum mengumumkan harga resmi, Sony mengindikasikan bahwa PlayStation 6 akan dijual dengan harga premium yang mencerminkan teknologi canggih yang ditawarkan. Pre-order diperkirakan akan dibuka pada akhir 2027.</p>

<h3>Dampak pada Industri</h3>

<p>Pengumuman ini langsung mendapat respons dari kompetitor. Microsoft mengisyaratkan akan mengumumkan Xbox generasi berikutnya dalam waktu dekat, sementara Nintendo tetap fokus pada inovasi gameplay dibanding spesifikasi hardware.</p>

<p>Analis industri memperkirakan PlayStation 6 akan mendorong adopsi TV 8K dan teknologi display terbaru, menciptakan ekosistem gaming yang benar-benar next-generation.</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=1200&h=600&fit=crop',
            'author_id' => $author1->id,
            'type' => 'news',
            'status' => 'published',
            'is_featured' => false,
            'reading_time' => 7,
            'views_count' => 19340,
            'meta' => [
                'title' => 'PlayStation 6 Rilis 2028 dengan Dukungan 8K Gaming',
                'description' => 'Sony resmi umumkan PlayStation 6 dengan teknologi revolusioner: 8K gaming, ray tracing real-time, dan neural rendering.',
                'keywords' => 'playstation 6, ps6, sony, 8k gaming, ray tracing, konsol gaming'
            ],
            'social_meta' => [
                'og_title' => 'RESMI: PlayStation 6 Rilis 2028 dengan 8K Gaming!',
                'og_description' => 'Sony umumkan konsol next-gen dengan teknologi revolusioner. 8K gaming dan ray tracing real-time jadi kenyataan!',
                'og_image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=1200&h=630&fit=crop',
                'twitter_title' => 'PlayStation 6 Coming 2028!',
                'twitter_description' => '8K gaming, ray tracing real-time, neural rendering. The future of gaming is here!',
                'twitter_image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=1200&h=630&fit=crop'
            ],
            'published_at' => now()->subHours(6),
            'created_at' => now()->subHours(6),
            'updated_at' => now()->subHours(6),
        ]);
        $article3->categories()->attach([$newsCategory->id]);

        // Article 4: Game Guide
        $article4 = Article::create([
            'title' => 'Panduan Lengkap Menjadi Pro Player Valorant: Tips dari Radiant Rank',
            'slug' => 'panduan-lengkap-menjadi-pro-player-valorant-tips-radiant-rank',
            'subtitle' => 'Strategi dan teknik yang digunakan pemain profesional untuk mencapai rank tertinggi',
            'excerpt' => 'Pelajari rahasia para pro player Valorant untuk meningkatkan skill dan mencapai rank Radiant dengan panduan komprehensif ini.',
            'content' => '<h2>Jalan Menuju Radiant Rank</h2>

<p>Mencapai rank Radiant di Valorant bukanlah hal yang mudah. Hanya 0.03% dari seluruh player base yang berhasil mencapai rank tertinggi ini. Namun, dengan dedikasi, latihan yang tepat, dan strategi yang benar, impian menjadi pro player bisa terwujud.</p>

<h3>1. Fundamental Aim Training</h3>

<p>Aim adalah fondasi dari semua skill di Valorant. Tanpa aim yang baik, strategi terbaik pun tidak akan berguna.</p>

<h4>Routine Harian:</h4>
<ul>
<li><strong>Kovaaks FPS Aim Trainer:</strong> 30 menit setiap hari</li>
<li><strong>Aim Lab:</strong> Fokus pada precision dan flicking</li>
<li><strong>Deathmatch:</strong> 2-3 game sebelum ranked</li>
<li><strong>Range Practice:</strong> 100 kills dengan Vandal dan Phantom</li>
</ul>

<h4>Pengaturan Optimal:</h4>
<ul>
<li><strong>Sensitivity:</strong> 0.3-0.5 dengan 800 DPI</li>
<li><strong>Crosshair:</strong> Static, warna cyan, thickness 1</li>
<li><strong>FPS:</strong> Minimal 144fps stabil</li>
</ul>

<h3>2. Game Sense dan Positioning</h3>

<p>Aim yang bagus tidak cukup tanpa game sense yang baik. Positioning adalah kunci untuk memenangkan duel dan round.</p>

<h4>Prinsip Positioning:</h4>
<ul>
<li><strong>Jiggle Peeking:</strong> Gather informasi tanpa commit</li>
<li><strong>Off-angles:</strong> Posisi yang tidak terduga musuh</li>
<li><strong>Crossfire:</strong> Koordinasi dengan teammate</li>
<li><strong>Escape Route:</strong> Selalu punya jalan keluar</li>
</ul>

<blockquote>
"Game sense adalah yang membedakan player biasa dengan pro player. Aim bisa dilatih, tapi game sense butuh pengalaman dan analisis mendalam." - TenZ, Sentinels
</blockquote>

<h3>3. Agent Mastery</h3>

<p>Fokus pada 2-3 agent dan kuasai sepenuhnya daripada main semua agent setengah-setengah.</p>

<h4>Rekomendasi Agent untuk Pemula:</h4>
<ul>
<li><strong>Duelist:</strong> Reyna (self-sufficient)</li>
<li><strong>Controller:</strong> Omen (versatile)</li>
<li><strong>Initiator:</strong> Sova (info gathering)</li>
<li><strong>Sentinel:</strong> Sage (team support)</li>
</ul>

<h3>4. Mental Game dan Komunikasi</h3>

<p>Aspek mental sering diabaikan padahal sangat crucial untuk improvement jangka panjang.</p>

<h4>Tips Mental:</h4>
<ul>
<li><strong>Stay Positive:</strong> Jangan flame teammate</li>
<li><strong>Learn from Losses:</strong> Review demo setiap kekalahan</li>
<li><strong>Take Breaks:</strong> Istirahat setelah 2 loss berturut-turut</li>
<li><strong>Communicate:</strong> Callout yang jelas dan singkat</li>
</ul>

<h3>5. Analisis dan Review</h3>

<p>Pro player selalu menganalisis gameplay mereka untuk terus improve.</p>

<h4>Tools untuk Review:</h4>
<ul>
<li><strong>Valorant Tracker:</strong> Statistik detail</li>
<li><strong>Blitz.gg:</strong> Real-time analysis</li>
<li><strong>Demo Review:</strong> Record dan tonton ulang</li>
<li><strong>Coach Analysis:</strong> Hire coach untuk feedback</li>
</ul>

<h3>6. Routine Latihan Pro Player</h3>

<p>Berikut adalah routine harian yang digunakan oleh pro player:</p>

<h4>Warm-up (45 menit):</h4>
<ul>
<li>Range: 15 menit</li>
<li>Aim trainer: 15 menit</li>
<li>Deathmatch: 15 menit</li>
</ul>

<h4>Ranked Games (3-4 jam):</h4>
<ul>
<li>Focus pada improvement, bukan RR</li>
<li>Analisis setiap round</li>
<li>Komunikasi aktif dengan team</li>
</ul>

<h4>Review Session (30 menit):</h4>
<ul>
<li>Tonton demo game terakhir</li>
<li>Catat mistake dan area improvement</li>
<li>Plan untuk session berikutnya</li>
</ul>

<h3>Kesimpulan</h3>

<p>Menjadi pro player Valorant membutuhkan dedikasi, konsistensi, dan mindset yang benar. Fokus pada improvement jangka panjang daripada rank jangka pendek. Dengan mengikuti panduan ini dan berlatih secara konsisten, rank Radiant bukanlah hal yang mustahil.</p>

<p><em>Ingat: "Practice does not make perfect. Perfect practice makes perfect."</em></p>',
            'featured_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=600&fit=crop',
            'author_id' => $author1->id,
            'type' => 'guide',
            'status' => 'published',
            'is_featured' => false,
            'reading_time' => 12,
            'views_count' => 8920,
            'meta' => [
                'title' => 'Panduan Lengkap Menjadi Pro Player Valorant - Tips Radiant',
                'description' => 'Pelajari strategi dan teknik pro player Valorant untuk mencapai rank Radiant. Panduan lengkap dari aim training hingga game sense.',
                'keywords' => 'valorant guide, pro player tips, radiant rank, aim training, game sense'
            ],
            'social_meta' => [
                'og_title' => 'Cara Menjadi Pro Player Valorant: Panduan Lengkap',
                'og_description' => 'Tips dan strategi dari pro player untuk mencapai rank Radiant di Valorant. Dari aim training hingga mental game!',
                'og_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=630&fit=crop',
                'twitter_title' => 'Valorant Pro Player Guide',
                'twitter_description' => 'Rahasia mencapai rank Radiant dari pro player. Panduan lengkap untuk improve skill Valorant!',
                'twitter_image' => 'https://images.unsplash.com/photo-1542751371-adc38448a05e?w=1200&h=630&fit=crop'
            ],
            'published_at' => now()->subHours(3),
            'created_at' => now()->subHours(3),
            'updated_at' => now()->subHours(3),
        ]);
        $article4->categories()->attach([$guideCategory->id]);
        $article4->tags()->attach([$tags['fps']->id, $tags['multiplayer']->id, $tags['strategy']->id]);

        // Article 5: Indie Game Review
        $article5 = Article::create([
            'title' => 'Pizza Tower: Masterpiece Indie yang Menghidupkan Kembali Era Platformer 90an',
            'slug' => 'pizza-tower-masterpiece-indie-menghidupkan-kembali-platformer-90an',
            'subtitle' => 'Game indie yang membuktikan bahwa inovasi masih bisa ditemukan dalam genre klasik',
            'excerpt' => 'Pizza Tower menghadirkan pengalaman platformer yang unik dengan animasi hand-drawn yang memukau dan gameplay yang adiktif.',
            'content' => '<h2>Kembalinya Era Keemasan Platformer</h2>

<p>Pizza Tower adalah bukti nyata bahwa genre platformer 2D masih memiliki tempat di hati para gamer modern. Dikembangkan oleh Tour De Pizza, game indie ini berhasil menangkap esensi platformer klasik tahun 90an sambil menghadirkan inovasi yang segar dan menghibur.</p>

<h3>Animasi Hand-Drawn yang Memukau</h3>

<p>Hal pertama yang langsung mencuri perhatian dari Pizza Tower adalah gaya visual yang unik. Setiap frame animasi digambar tangan dengan detail yang luar biasa, menciptakan gerakan yang fluid dan ekspresif. Karakter Peppino Spaghetti bergerak dengan energi yang menular, setiap ekspresi wajahnya berhasil menyampaikan emosi dengan sempurna.</p>

<blockquote>
"Pizza Tower membuktikan bahwa dengan kreativitas dan dedikasi, developer indie bisa menciptakan karya yang melampaui ekspektasi."
</blockquote>

<h3>Gameplay yang Adiktif</h3>

<p>Gameplay Pizza Tower menggabungkan elemen platformer klasik dengan twist modern yang cerdas. Sistem momentum yang unik membuat setiap level terasa seperti roller coaster yang mendebarkan. Pemain harus berlari, melompat, dan menyerang dengan ritme yang tepat untuk mencapai skor tertinggi.</p>

<h4>Fitur Gameplay Unggulan:</h4>
<ul>
<li><strong>Momentum System:</strong> Semakin cepat bergerak, semakin kuat serangan</li>
<li><strong>Combo System:</strong> Chain attacks untuk skor maksimal</li>
<li><strong>Multiple Paths:</strong> Setiap level punya rute alternatif</li>
<li><strong>Boss Battles:</strong> Pertarungan boss yang kreatif dan menantang</li>
</ul>

<h3>Soundtrack yang Memorable</h3>

<p>Musik Pizza Tower layak mendapat pujian tersendiri. Setiap track berhasil menangkap mood dan energi level dengan sempurna. Dari melodi yang catchy hingga beat yang menghentak, soundtrack ini akan terus berputar di kepala bahkan setelah berhenti bermain.</p>

<h3>Level Design yang Brilian</h3>

<p>Setiap level dirancang dengan cermat untuk mengoptimalkan fun factor. Tidak ada momen yang terasa membosankan atau repetitif. Developer berhasil menciptakan variasi yang cukup dalam setiap stage, dari puzzle platforming hingga chase sequence yang menegangkan.</p>

<h3>Kesimpulan</h3>

<p>Pizza Tower adalah reminder bahwa game tidak perlu budget AAA untuk memberikan pengalaman yang luar biasa. Dengan passion, kreativitas, dan execution yang tepat, developer indie bisa menciptakan masterpiece yang akan dikenang bertahun-tahun.</p>

<p>Bagi penggemar platformer klasik atau siapa saja yang mencari game dengan personality yang kuat, Pizza Tower adalah must-play yang tidak boleh dilewatkan.</p>

<p><strong>Rating: 8.5/10</strong></p>

<p><em>Pizza Tower tersedia di Steam, Nintendo Switch, dan akan segera hadir di konsol lainnya dengan harga $19.99.</em></p>',
            'featured_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=1200&h=600&fit=crop',
            'author_id' => $author1->id,
            'type' => 'review',
            'status' => 'published',
            'is_featured' => false,
            'reading_time' => 10,
            'views_count' => 12450,
            'rating' => 8.5,
            'pros' => [
                'Animasi hand-drawn yang memukau',
                'Gameplay yang unik dan adiktif',
                'Soundtrack yang memorable',
                'Level design yang brilian',
                'Personality yang kuat dan menghibur'
            ],
            'cons' => [
                'Kurva belajar yang cukup steep',
                'Beberapa level bisa terasa terlalu chaotic',
                'Durasi permainan relatif pendek'
            ],
            'meta' => [
                'title' => 'Review Pizza Tower - Masterpiece Indie Platformer 2023',
                'description' => 'Review lengkap Pizza Tower, game indie platformer yang menghidupkan kembali era keemasan genre dengan animasi hand-drawn yang memukau.',
                'keywords' => 'pizza tower, indie game, platformer, review, tour de pizza, hand drawn animation'
            ],
            'social_meta' => [
                'og_title' => 'Pizza Tower Review: Indie Platformer Terbaik 2023',
                'og_description' => 'Game indie yang membuktikan bahwa kreativitas mengalahkan budget besar. Animasi hand-drawn yang memukau dan gameplay yang adiktif!',
                'og_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=1200&h=630&fit=crop',
                'twitter_title' => 'Pizza Tower: Indie Masterpiece!',
                'twitter_description' => 'Platformer indie terbaik dengan animasi hand-drawn yang luar biasa. Rating: 8.5/10',
                'twitter_image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?w=1200&h=630&fit=crop'
            ],
            'published_at' => now()->subHours(1),
            'created_at' => now()->subHours(1),
            'updated_at' => now()->subHours(1),
        ]);
        $article5->categories()->attach([$reviewCategory->id]);
        $article5->tags()->attach([$tags['indie']->id, $tags['platformer']->id, $tags['single-player']->id]);
    }
}

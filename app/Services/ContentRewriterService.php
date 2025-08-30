<?php

namespace App\Services;

use OpenAI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ContentRewriterService
{
    private array $gamingKeywords = [
        'mobile legends', 'pubg', 'valorant', 'dota 2', 'free fire', 
        'esports', 'gaming', 'tournament', 'championship', 'streamer',
        'gameplay', 'update', 'patch', 'skin', 'hero', 'champion'
    ];

    /**
     * Rewrite content using OpenAI GPT.
     */
    public function rewriteContent(string $originalContent, string $title = '', string $category = 'gaming'): array
    {
        try {
            // Check if OpenAI is configured
            if (!config('openai.api_key')) {
                Log::warning('OpenAI API key not configured, using fallback rewriting');
                return $this->fallbackRewrite($originalContent, $title);
            }

            $prompt = $this->buildPrompt($originalContent, $title, $category);
            
            $client = OpenAI::client(config('services.openai.api_key'));
            
            $response = $client->chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'Anda adalah seorang penulis konten gaming Indonesia yang ahli. Tulis ulang konten dengan gaya yang menarik, informatif, dan SEO-friendly dalam bahasa Indonesia.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'max_tokens' => 2000,
                'temperature' => 0.7,
            ]);

            $rewrittenContent = $response->choices[0]->message->content;
            
            // Parse the response to extract title, excerpt, and content
            $parsed = $this->parseAIResponse($rewrittenContent);
            
            return [
                'content' => $parsed['content'],
                'excerpt' => $parsed['excerpt'],
                'title' => $parsed['title'] ?: $title,
                'rewritten_by' => 'openai',
                'original_length' => strlen($originalContent),
                'rewritten_length' => strlen($parsed['content']),
            ];

        } catch (\Exception $e) {
            Log::error('OpenAI rewriting failed: ' . $e->getMessage());
            return $this->fallbackRewrite($originalContent, $title);
        }
    }

    /**
     * Build prompt for AI rewriting.
     */
    private function buildPrompt(string $content, string $title, string $category): string
    {
        $cleanContent = $this->cleanContent($content);
        
        return "Tulis ulang artikel berita gaming berikut dalam bahasa Indonesia yang menarik dan SEO-friendly:

JUDUL ASLI: {$title}
KATEGORI: {$category}

KONTEN ASLI:
{$cleanContent}

INSTRUKSI:
1. Tulis ulang dengan gaya yang fresh dan menarik untuk pembaca gaming Indonesia
2. Pertahankan semua fakta dan informasi penting
3. Gunakan bahasa yang mudah dipahami tapi tetap profesional
4. Tambahkan konteks gaming Indonesia jika relevan
5. Buat struktur yang jelas dengan paragraf yang mudah dibaca
6. Optimasi untuk SEO dengan keyword yang natural

FORMAT RESPONSE:
JUDUL: [judul baru yang menarik]
EXCERPT: [ringkasan 2-3 kalimat]
KONTEN: [artikel lengkap yang sudah ditulis ulang]";
    }

    /**
     * Clean and prepare content for rewriting.
     */
    private function cleanContent(string $content): string
    {
        // Remove HTML tags
        $content = strip_tags($content);
        
        // Remove extra whitespace
        $content = preg_replace('/\s+/', ' ', $content);
        
        // Remove common unwanted phrases
        $unwantedPhrases = [
            'Baca juga:',
            'Artikel terkait:',
            'Sumber:',
            'Editor:',
            'Reporter:',
            'Foto:',
            'Video:',
        ];
        
        foreach ($unwantedPhrases as $phrase) {
            $content = str_ireplace($phrase, '', $content);
        }
        
        return trim($content);
    }

    /**
     * Parse AI response to extract structured content.
     */
    private function parseAIResponse(string $response): array
    {
        $lines = explode("\n", $response);
        $result = [
            'title' => '',
            'excerpt' => '',
            'content' => '',
        ];
        
        $currentSection = 'content';
        $contentLines = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (str_starts_with($line, 'JUDUL:')) {
                $result['title'] = trim(str_replace('JUDUL:', '', $line));
                continue;
            }
            
            if (str_starts_with($line, 'EXCERPT:')) {
                $result['excerpt'] = trim(str_replace('EXCERPT:', '', $line));
                continue;
            }
            
            if (str_starts_with($line, 'KONTEN:')) {
                $currentSection = 'content';
                continue;
            }
            
            if ($currentSection === 'content' && !empty($line)) {
                $contentLines[] = $line;
            }
        }
        
        $result['content'] = implode("\n\n", $contentLines);
        
        // Fallback if parsing failed
        if (empty($result['content'])) {
            $result['content'] = $response;
        }
        
        if (empty($result['excerpt'])) {
            $result['excerpt'] = Str::limit(strip_tags($result['content']), 200);
        }
        
        return $result;
    }

    /**
     * Enhanced fallback rewriting method when AI is not available.
     */
    private function fallbackRewrite(string $originalContent, string $title): array
    {
        $cleanContent = $this->cleanContent($originalContent);
        
        // Create engaging gaming content
        $rewritten = $this->createEngagingContent($cleanContent, $title);
        
        return [
            'content' => $rewritten,
            'excerpt' => $this->generateEngagingExcerpt($rewritten, $title),
            'title' => $this->enhanceTitle($title),
            'rewritten_by' => 'enhanced_fallback',
            'original_length' => strlen($originalContent),
            'rewritten_length' => strlen($rewritten),
        ];
    }

    /**
     * Apply basic text rewriting transformations.
     */
    private function applyBasicRewriting(string $content): string
    {
        // Split into sentences
        $sentences = preg_split('/[.!?]+/', $content);
        $rewrittenSentences = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) continue;
            
            // Apply basic transformations
            $rewritten = $this->transformSentence($sentence);
            $rewrittenSentences[] = $rewritten;
        }
        
        return implode('. ', $rewrittenSentences) . '.';
    }

    /**
     * Transform individual sentence.
     */
    private function transformSentence(string $sentence): string
    {
        // Simple synonym replacements for gaming content
        $synonyms = [
            'game' => ['permainan', 'game'],
            'player' => ['pemain', 'gamer'],
            'update' => ['pembaruan', 'update'],
            'tournament' => ['turnamen', 'kompetisi'],
            'team' => ['tim', 'squad'],
            'match' => ['pertandingan', 'match'],
            'win' => ['menang', 'kemenangan'],
            'lose' => ['kalah', 'kekalahan'],
        ];
        
        foreach ($synonyms as $original => $replacements) {
            if (stripos($sentence, $original) !== false) {
                $replacement = $replacements[array_rand($replacements)];
                $sentence = str_ireplace($original, $replacement, $sentence);
            }
        }
        
        return $sentence;
    }

    /**
     * Generate SEO-friendly excerpt.
     */
    public function generateExcerpt(string $content, int $maxLength = 160): string
    {
        $cleanContent = strip_tags($content);
        
        // Try to find a natural break point
        $sentences = preg_split('/[.!?]+/', $cleanContent);
        $excerpt = '';
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (empty($sentence)) continue;
            
            if (strlen($excerpt . $sentence) <= $maxLength) {
                $excerpt .= ($excerpt ? '. ' : '') . $sentence;
            } else {
                break;
            }
        }
        
        // Fallback to simple truncation
        if (empty($excerpt)) {
            $excerpt = Str::limit($cleanContent, $maxLength);
        }
        
        return $excerpt;
    }

    /**
     * Check content quality and suggest improvements.
     */
    public function analyzeContent(string $content): array
    {
        $analysis = [
            'word_count' => str_word_count($content),
            'character_count' => strlen($content),
            'paragraph_count' => substr_count($content, "\n\n") + 1,
            'gaming_keywords_found' => [],
            'readability_score' => 0,
            'suggestions' => [],
        ];
        
        // Check for gaming keywords
        foreach ($this->gamingKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $analysis['gaming_keywords_found'][] = $keyword;
            }
        }
        
        // Basic readability analysis
        $avgWordsPerSentence = $analysis['word_count'] / (substr_count($content, '.') + 1);
        $analysis['readability_score'] = max(0, min(100, 100 - ($avgWordsPerSentence * 2)));
        
        // Generate suggestions
        if ($analysis['word_count'] < 300) {
            $analysis['suggestions'][] = 'Konten terlalu pendek, pertimbangkan untuk menambah detail';
        }
        
        if (empty($analysis['gaming_keywords_found'])) {
            $analysis['suggestions'][] = 'Tambahkan lebih banyak keyword gaming untuk SEO';
        }
        
        if ($avgWordsPerSentence > 20) {
            $analysis['suggestions'][] = 'Kalimat terlalu panjang, pertimbangkan untuk memecah menjadi kalimat yang lebih pendek';
        }
        
        return $analysis;
    }

    /**
     * Generate gaming-focused meta description with SEO hashtags.
     */
    public function generateMetaDescription(string $title, string $content): string
    {
        $keywords = array_intersect($this->gamingKeywords, 
            array_map('strtolower', str_word_count($title . ' ' . $content, 1)));
        
        $excerpt = $this->generateExcerpt($content, 120);
        
        // Add gaming context if keywords found
        if (!empty($keywords)) {
            $keywordText = implode(', ', array_slice($keywords, 0, 3));
            $excerpt = "Berita gaming terbaru tentang {$keywordText}. {$excerpt}";
        }
        
        // Add trending hashtags for SEO
        $hashtags = $this->generateSEOHashtags($title);
        $excerpt .= ' ' . implode(' ', array_slice($hashtags, 0, 3));
        
        return Str::limit($excerpt, 160);
    }

    /**
     * Create engaging gaming content with proper HTML structure and formatting.
     */
    private function createEngagingContent(string $content, string $title): string
    {
        // Split content into paragraphs
        $paragraphs = array_filter(explode("\n", $content));
        $htmlContent = [];
        
        // Add main heading
        $htmlContent[] = '<h2>' . $this->extractMainHeading($title) . '</h2>';
        
        // Add engaging introduction
        $intro = $this->createEngagingIntro($title, $paragraphs[0] ?? '');
        $htmlContent[] = '<p>' . $intro . '</p>';
        
        // Process content into sections with proper HTML
        $sections = $this->createContentSections($paragraphs, $title);
        $htmlContent = array_merge($htmlContent, $sections);
        
        // Add source attribution
        $sourceAttribution = $this->createSourceAttribution();
        $htmlContent[] = $sourceAttribution;
        
        return implode("\n\n", $htmlContent);
    }

    /**
     * Create engaging introduction for gaming articles.
     */
    private function createEngagingIntro(string $title, string $firstParagraph): string
    {
        $viralIntros = [
            '🔥 BREAKING! Dunia gaming Indonesia kembali berguncang!',
            '⚡ VIRAL! Berita ini bikin komunitas gaming Indonesia heboh total!',
            '🚨 ALERT GAMER! Info penting yang wajib kalian tahu sekarang juga!',
            '💥 BOOM! Kabar mengejutkan yang bakal bikin kalian speechless!',
            '🎮 GAMER INDONESIA MERAPAT! Ada kabar gila yang harus kalian dengar!',
            '🔥 HOT NEWS! Informasi terbaru yang bikin timeline gaming Indonesia ricuh!',
            '⚡ TRENDING NOW! Berita yang lagi viral di kalangan gamer Indonesia!',
            '🚨 URGENT! Para gamer wajib baca ini sebelum terlambat!'
        ];
        
        $selectedIntro = $viralIntros[array_rand($viralIntros)];
        
        // Extract key information from title
        $keyInfo = $this->extractKeyInfo($title);
        
        $engagingContent = $this->enhanceParagraph($firstParagraph);
        
        return "{$selectedIntro} {$keyInfo} yang lagi jadi perbincangan hangat. {$engagingContent}";
    }

    /**
     * Create engaging conclusion for gaming articles.
     */
    private function createEngagingConclusion(string $title): string
    {
        $conclusions = [
            'Perkembangan ini tentunya menjadi kabar gembira bagi para gamer Indonesia yang selalu menantikan inovasi terbaru di dunia gaming.',
            'Dengan adanya update ini, diharapkan pengalaman gaming para pemain akan semakin memuaskan dan kompetitif.',
            'Para gamer Indonesia dapat terus mengikuti perkembangan terbaru melalui berbagai platform gaming dan media sosial resmi.',
            'Industri gaming Indonesia terus menunjukkan pertumbuhan yang positif dengan berbagai inovasi dan kolaborasi strategis.',
            'Komunitas gaming tanah air semakin solid dan siap menghadapi tantangan baru di era digital ini.'
        ];
        
        return $conclusions[array_rand($conclusions)];
    }

    /**
     * Enhance paragraph with gaming context and better structure.
     */
    private function enhanceParagraph(string $paragraph): string
    {
        // Add gaming context words with more engaging terms
        $gamingContext = [
            'developer' => 'developer game kece',
            'update' => 'update gila-gilaan',
            'player' => 'para gamer keren',
            'game' => 'permainan epic',
            'feature' => 'fitur keren abis',
            'community' => 'komunitas gaming solid',
            'tournament' => 'turnamen esports seru',
            'team' => 'tim pro player',
            'skin' => 'skin kece badai',
            'character' => 'karakter overpowered'
        ];
        
        foreach ($gamingContext as $original => $enhanced) {
            $paragraph = str_ireplace($original, $enhanced, $paragraph);
        }
        
        // Add viral gaming slang and expressions
        $viralExpressions = [
            'sangat bagus' => 'keren banget sih!',
            'menarik' => 'bikin nagih!',
            'populer' => 'viral abis!',
            'banyak' => 'buanyak banget!',
            'hebat' => 'gokil parah!',
            'luar biasa' => 'insane banget!',
            'mengejutkan' => 'bikin melongo!'
        ];
        
        foreach ($viralExpressions as $original => $viral) {
            if (rand(1, 4) === 1) { // 25% chance
                $paragraph = str_ireplace($original, $viral, $paragraph);
            }
        }
        
        // Add engaging transitions with gaming flavor
        $transitions = [
            'Nah, yang bikin makin seru,', 'Tapi tunggu dulu,', 'Yang paling epic,', 
            'Dari sisi gameplay nih,', 'Buat kalian para gamer,', 'Yang bikin hype,',
            'Gak cuma itu doang,', 'Yang paling ditunggu-tunggu,', 'Seru banget kan?'
        ];
        
        // Randomly add transitions to some sentences
        if (rand(1, 3) === 1) {
            $sentences = explode('.', $paragraph);
            if (count($sentences) > 1) {
                $randomIndex = rand(1, count($sentences) - 1);
                $transition = $transitions[array_rand($transitions)];
                $sentences[$randomIndex] = ' ' . $transition . ' ' . trim($sentences[$randomIndex]);
                $paragraph = implode('.', $sentences);
            }
        }
        
        return $paragraph;
    }

    /**
     * Extract key information from title for engaging intro.
     */
    private function extractKeyInfo(string $title): string
    {
        // Look for game names, companies, or key features
        $gameNames = ['Mobile Legends', 'PUBG', 'Valorant', 'Free Fire', 'Dota 2', 'Call of Duty', 'Honkai', 'Resident Evil'];
        $companies = ['Activision', 'Moonton', 'Riot Games', 'Capcom', 'Tencent', 'Garena'];
        
        foreach ($gameNames as $game) {
            if (stripos($title, $game) !== false) {
                return "kabar terbaru seputar {$game}";
            }
        }
        
        foreach ($companies as $company) {
            if (stripos($title, $company) !== false) {
                return "pengumuman penting dari {$company}";
            }
        }
        
        // Fallback to generic gaming news
        return "berita gaming yang menarik perhatian";
    }

    /**
     * Generate engaging excerpt with gaming context.
     */
    private function generateEngagingExcerpt(string $content, string $title): string
    {
        $keyInfo = $this->extractKeyInfo($title);
        
        // Get first meaningful sentence
        $sentences = preg_split('/[.!?]+/', strip_tags($content));
        $firstSentence = '';
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (strlen($sentence) > 30) {
                $firstSentence = $sentence;
                break;
            }
        }
        
        $excerpt = "Berita gaming terbaru mengungkap {$keyInfo}. {$firstSentence}";
        
        return Str::limit($excerpt, 200);
    }

    /**
     * Enhance title to make it more engaging.
     */
    private function enhanceTitle(string $title): string
    {
        // Add engaging prefixes for gaming news
        $prefixes = [
            'BREAKING:', 'UPDATE:', 'TERBARU:', 'HOT NEWS:', 'GAMING NEWS:'
        ];
        
        // Add engaging suffixes
        $suffixes = [
            '- Gamer Indonesia Wajib Tahu!',
            '- Update Terbaru yang Dinanti!',
            '- Kabar Gembira untuk Gamer!',
            '- Perkembangan Mengejutkan!',
            '- Info Penting untuk Gamers!'
        ];
        
        // Check if title already has engaging elements
        $hasPrefix = false;
        foreach ($prefixes as $prefix) {
            if (stripos($title, str_replace(':', '', $prefix)) !== false) {
                $hasPrefix = true;
                break;
            }
        }
        
        // Add prefix if not present
        if (!$hasPrefix && rand(1, 2) === 1) {
            $prefix = $prefixes[array_rand($prefixes)];
            $title = $prefix . ' ' . $title;
        }
        
        // Add suffix if title is not too long
        if (strlen($title) < 80 && rand(1, 3) === 1) {
            $suffix = $suffixes[array_rand($suffixes)];
            $title = $title . ' ' . $suffix;
        }
        
        return $title;
    }

    /**
     * Extract main heading from title.
     */
    private function extractMainHeading(string $title): string
    {
        // Remove common prefixes and suffixes for cleaner heading
        $cleanTitle = $title;
        
        $prefixesToRemove = ['BREAKING:', 'UPDATE:', 'TERBARU:', 'HOT NEWS:', 'GAMING NEWS:'];
        foreach ($prefixesToRemove as $prefix) {
            $cleanTitle = str_ireplace($prefix, '', $cleanTitle);
        }
        
        // Extract main part before any dash
        $parts = explode(' - ', $cleanTitle);
        $mainHeading = trim($parts[0]);
        
        return $mainHeading ?: 'Berita Gaming Terbaru';
    }

    /**
     * Create content sections with proper HTML structure.
     */
    private function createContentSections(array $paragraphs, string $title): array
    {
        $sections = [];
        $sectionCount = 0;
        
        // Create subheadings based on content
        $subheadings = $this->generateSubheadings($title);
        
        // Add detailed analysis section
        $sections[] = '<h3>' . $subheadings[0] . '</h3>';
        
        // Process original paragraphs with enhancements
        foreach ($paragraphs as $index => $paragraph) {
            if ($index === 0) continue; // Skip first paragraph as it's used in intro
            
            $paragraph = trim($paragraph);
            if (empty($paragraph)) continue;
            
            // Add subheading every 2 paragraphs for better structure
            if ($index % 2 === 1 && $sectionCount + 1 < count($subheadings)) {
                $sectionCount++;
                $sections[] = '<h3>' . $subheadings[$sectionCount] . '</h3>';
            }
            
            // Enhance and wrap paragraph
            $enhanced = $this->enhanceParagraph($paragraph);
            $sections[] = '<p>' . $enhanced . '</p>';
            
            // Add additional detailed content after each paragraph
            $additionalContent = $this->generateAdditionalContent($title, $paragraph, $index);
            if ($additionalContent) {
                $sections[] = '<p>' . $additionalContent . '</p>';
            }
            
            // Add blockquote occasionally for engagement
            if ($index % 3 === 0 && strlen($enhanced) > 100) {
                $quote = $this->createEngagingQuote($title);
                if ($quote) {
                    $sections[] = '<blockquote>' . $quote . '</blockquote>';
                }
            }
            
            // Add detailed breakdown every few paragraphs
            if ($index % 4 === 0) {
                $breakdown = $this->createDetailedBreakdown($title, $index);
                if ($breakdown) {
                    $sections[] = '<p>' . $breakdown . '</p>';
                }
            }
        }
        
        // Add comprehensive analysis section
        $sections[] = '<h3>Analisis Mendalam</h3>';
        $analysis = $this->createComprehensiveAnalysis($title);
        $sections[] = '<p>' . $analysis . '</p>';
        
        // Add community reaction section
        $sections[] = '<h3>Reaksi Komunitas Gaming Indonesia</h3>';
        $communityReaction = $this->createCommunityReaction($title);
        $sections[] = '<p>' . $communityReaction . '</p>';
        
        // Add future implications
        $sections[] = '<h3>Dampak untuk Masa Depan Gaming</h3>';
        $futureImplications = $this->createFutureImplications($title);
        $sections[] = '<p>' . $futureImplications . '</p>';
        
        // Add conclusion section
        $sections[] = '<h3>Kesimpulan</h3>';
        $conclusion = $this->createEngagingConclusion($title);
        $sections[] = '<p>' . $conclusion . '</p>';
        
        return $sections;
    }

    /**
     * Generate relevant subheadings for gaming content.
     */
    private function generateSubheadings(string $title): array
    {
        $gameNames = ['Mobile Legends', 'PUBG', 'Valorant', 'Free Fire', 'Dota 2', 'Call of Duty', 'Honkai', 'Resident Evil'];
        $companies = ['Activision', 'Moonton', 'Riot Games', 'Capcom', 'Tencent', 'Garena'];
        
        $subheadings = [];
        
        // Game-specific subheadings
        foreach ($gameNames as $game) {
            if (stripos($title, $game) !== false) {
                $subheadings = [
                    "Apa yang Baru di {$game}?",
                    "Dampak untuk Para Gamer Indonesia",
                    "Fitur dan Gameplay Terbaru",
                    "Respons Komunitas Gaming"
                ];
                break;
            }
        }
        
        // Company-specific subheadings
        if (empty($subheadings)) {
            foreach ($companies as $company) {
                if (stripos($title, $company) !== false) {
                    $subheadings = [
                        "Pengumuman Resmi dari {$company}",
                        "Dampak untuk Industri Gaming",
                        "Reaksi Para Gamer",
                        "Apa Selanjutnya?"
                    ];
                    break;
                }
            }
        }
        
        // Generic gaming subheadings
        if (empty($subheadings)) {
            $subheadings = [
                "Detail Lengkap Pengumuman",
                "Dampak untuk Komunitas Gaming",
                "Fitur dan Inovasi Terbaru",
                "Respons dan Antusiasme Gamer"
            ];
        }
        
        return $subheadings;
    }

    /**
     * Create engaging quotes for articles.
     */
    private function createEngagingQuote(string $title): ?string
    {
        $quotes = [
            "Perkembangan ini menandai era baru dalam industri gaming Indonesia yang semakin kompetitif dan inovatif.",
            "Para gamer Indonesia kini memiliki lebih banyak pilihan untuk menikmati pengalaman gaming yang berkualitas tinggi.",
            "Industri gaming terus berkembang dengan pesat, memberikan dampak positif bagi ekosistem digital Indonesia.",
            "Komunitas gaming Indonesia semakin solid dan siap menghadapi tantangan di era digital ini.",
            "Inovasi terbaru ini membuktikan bahwa gaming bukan hanya hiburan, tapi juga industri yang serius."
        ];
        
        // Return random quote 30% of the time
        return rand(1, 10) <= 3 ? $quotes[array_rand($quotes)] : null;
    }

    /**
     * Generate additional content to expand articles.
     */
    private function generateAdditionalContent(string $title, string $paragraph, int $index): ?string
    {
        $additionalContents = [
            "Hal ini tentunya menjadi sorotan utama di kalangan komunitas gaming Indonesia yang selalu antusias dengan perkembangan terbaru.",
            "Para content creator dan streamer gaming Indonesia juga turut memberikan respons positif terhadap kabar ini.",
            "Tidak hanya itu, para pro player Indonesia juga mulai mempersiapkan strategi baru untuk menghadapi perubahan ini.",
            "Komunitas gaming di media sosial seperti Twitter, Instagram, dan TikTok juga ramai membahas topik ini.",
            "Beberapa gaming influencer ternama Indonesia bahkan sudah mulai membuat konten khusus membahas hal ini.",
            "Tim esports Indonesia juga dikabarkan sedang menganalisis dampak dari perkembangan ini terhadap strategi mereka.",
            "Para gamer casual Indonesia pun tidak ketinggalan untuk ikut berdiskusi di berbagai forum gaming.",
            "Industri gaming Indonesia secara keseluruhan menunjukkan antusiasme yang tinggi terhadap berita ini."
        ];
        
        // Return additional content 40% of the time
        return rand(1, 10) <= 4 ? $additionalContents[array_rand($additionalContents)] : null;
    }

    /**
     * Create detailed breakdown for specific sections.
     */
    private function createDetailedBreakdown(string $title, int $index): ?string
    {
        $breakdowns = [
            "Dari sisi teknis, perkembangan ini menunjukkan komitmen developer untuk terus berinovasi dan memberikan pengalaman gaming terbaik bagi para pemain di Indonesia.",
            "Aspek kompetitif dari update ini juga patut diperhatikan, karena dapat mengubah meta permainan yang sudah ada selama ini.",
            "Fitur-fitur baru yang dihadirkan tentunya telah melalui proses testing yang ketat untuk memastikan kualitas dan stabilitas gameplay.",
            "Dampak ekonomi dari perkembangan ini juga cukup signifikan, terutama bagi ekosistem esports dan content creator Indonesia.",
            "Dari perspektif teknologi, inovasi ini menunjukkan bagaimana industri gaming terus berkembang mengikuti perkembangan zaman.",
            "Strategi marketing yang diterapkan juga cukup menarik perhatian, dengan pendekatan yang lebih fokus pada komunitas lokal Indonesia."
        ];
        
        // Return breakdown 30% of the time
        return rand(1, 10) <= 3 ? $breakdowns[array_rand($breakdowns)] : null;
    }

    /**
     * Create comprehensive analysis section.
     */
    private function createComprehensiveAnalysis(string $title): string
    {
        $gameNames = ['Mobile Legends', 'PUBG', 'Valorant', 'Free Fire', 'Dota 2', 'Call of Duty', 'Honkai', 'Resident Evil'];
        $companies = ['Activision', 'Moonton', 'Riot Games', 'Capcom', 'Tencent', 'Garena'];
        
        $analysis = "Jika kita analisis lebih mendalam, perkembangan ini memiliki implikasi yang cukup luas untuk industri gaming Indonesia. ";
        
        // Add game-specific analysis
        foreach ($gameNames as $game) {
            if (stripos($title, $game) !== false) {
                $analysis .= "Khusus untuk {$game}, hal ini bisa jadi game changer yang signifikan bagi para pemain Indonesia. ";
                break;
            }
        }
        
        // Add company-specific analysis
        foreach ($companies as $company) {
            if (stripos($title, $company) !== false) {
                $analysis .= "Keputusan {$company} ini menunjukkan perhatian serius mereka terhadap pasar gaming Indonesia yang terus berkembang pesat. ";
                break;
            }
        }
        
        $analysis .= "Dari segi kompetitif, perubahan ini akan mempengaruhi strategi tim esports Indonesia dan dapat membuka peluang baru bagi para pemain untuk menunjukkan skill mereka. ";
        $analysis .= "Selain itu, dampaknya juga akan terasa di sektor content creation, dimana para streamer dan YouTuber gaming Indonesia akan memiliki konten baru yang menarik untuk dibagikan kepada audience mereka.";
        
        return $analysis;
    }

    /**
     * Create community reaction section.
     */
    private function createCommunityReaction(string $title): string
    {
        $reactions = [
            "Respons dari komunitas gaming Indonesia sangat beragam dan antusias. Di platform media sosial seperti Twitter dan Instagram, hashtag terkait topik ini langsung trending dan ramai diperbincangkan oleh para gamer.",
            "Para content creator gaming ternama Indonesia seperti Jess No Limit, Windah Basudara, dan MiawAug juga turut memberikan komentar positif melalui channel YouTube dan live streaming mereka.",
            "Forum-forum gaming Indonesia seperti Kaskus Gaming, Reddit Indonesia Gaming, dan berbagai grup Facebook gaming juga dipenuhi dengan diskusi hangat seputar kabar ini.",
            "Tim esports profesional Indonesia juga memberikan statement resmi melalui akun media sosial mereka, dengan sebagian besar menyambut positif perkembangan ini.",
            "Komunitas gaming di berbagai daerah Indonesia, mulai dari Jakarta, Surabaya, Bandung, hingga Medan, juga menunjukkan antusiasme yang tinggi melalui event-event gaming lokal."
        ];
        
        $selectedReaction = $reactions[array_rand($reactions)];
        
        $additionalReaction = " Para gamer Indonesia juga mulai membuat berbagai konten kreatif seperti meme, fan art, dan video reaction yang viral di media sosial. ";
        $additionalReaction .= "Tidak ketinggalan, para pro player Indonesia juga sudah mulai mempersiapkan strategi dan latihan intensif untuk menghadapi perubahan yang akan datang.";
        
        return $selectedReaction . $additionalReaction;
    }

    /**
     * Create future implications section.
     */
    private function createFutureImplications(string $title): string
    {
        $implications = "Melihat perkembangan ini, masa depan gaming Indonesia terlihat semakin cerah dan penuh dengan peluang menarik. ";
        
        $implications .= "Dari segi industri, hal ini dapat mendorong pertumbuhan ekosistem gaming lokal, mulai dari developer indie Indonesia hingga perusahaan gaming besar yang beroperasi di tanah air. ";
        
        $implications .= "Para talent gaming Indonesia juga akan memiliki lebih banyak kesempatan untuk berkembang, baik sebagai pro player, content creator, maupun dalam bidang-bidang terkait gaming lainnya seperti shoutcaster dan analyst. ";
        
        $implications .= "Selain itu, perkembangan ini juga dapat menarik lebih banyak investasi ke sektor gaming Indonesia, yang pada akhirnya akan menciptakan lebih banyak lapangan kerja dan peluang bisnis baru. ";
        
        $implications .= "Tidak menutup kemungkinan, Indonesia akan semakin diakui sebagai salah satu pasar gaming terpenting di Asia Tenggara, bahkan dunia.";
        
        return $implications;
    }

    /**
     * Generate SEO hashtags for viral content.
     */
    private function generateSEOHashtags(string $title): array
    {
        $gameNames = ['Mobile Legends', 'PUBG', 'Valorant', 'Free Fire', 'Dota 2', 'Call of Duty', 'Honkai', 'Resident Evil'];
        $companies = ['Activision', 'Moonton', 'Riot Games', 'Capcom', 'Tencent', 'Garena'];
        
        $hashtags = [];
        
        // Gaming hashtags yang viral
        $viralHashtags = [
            '#GamingIndonesia', '#EsportsID', '#GamersIndonesia', '#BeritaGaming',
            '#UpdateGaming', '#NewsGaming', '#GamingNews', '#EsportsNews',
            '#GamerID', '#IndonesianGamer', '#GameUpdate', '#GamingCommunity'
        ];
        
        // Game-specific hashtags
        foreach ($gameNames as $game) {
            if (stripos($title, $game) !== false) {
                $gameTag = '#' . str_replace(' ', '', $game);
                $hashtags[] = $gameTag;
                $hashtags[] = $gameTag . 'Indonesia';
                $hashtags[] = $gameTag . 'ID';
                break;
            }
        }
        
        // Company-specific hashtags
        foreach ($companies as $company) {
            if (stripos($title, $company) !== false) {
                $hashtags[] = '#' . str_replace(' ', '', $company);
                break;
            }
        }
        
        // Add trending gaming hashtags
        $trendingHashtags = [
            '#ViralGaming', '#TrendingNow', '#HotNews', '#BreakingNews',
            '#GamingUpdate', '#EsportsTrending', '#GamersUnite', '#GamingBuzz'
        ];
        
        // Merge and randomize
        $allHashtags = array_merge($hashtags, $viralHashtags, $trendingHashtags);
        shuffle($allHashtags);
        
        // Return top 8 hashtags for SEO
        return array_slice(array_unique($allHashtags), 0, 8);
    }

    /**
     * Generate comprehensive SEO metadata with hashtags.
     */
    public function generateSEOMetadata(string $title, string $content): array
    {
        $hashtags = $this->generateSEOHashtags($title);
        $keywords = $this->extractSEOKeywords($title, $content);
        
        return [
            'meta_title' => $this->enhanceTitle($title),
            'meta_description' => $this->generateMetaDescription($title, $content),
            'keywords' => implode(', ', $keywords),
            'hashtags' => $hashtags,
            'social_title' => $this->createSocialTitle($title),
            'social_description' => $this->createSocialDescription($title, $content),
            'og_tags' => $this->generateOpenGraphTags($title, $content),
            'twitter_tags' => $this->generateTwitterTags($title, $content)
        ];
    }

    /**
     * Extract SEO keywords from title and content.
     */
    private function extractSEOKeywords(string $title, string $content): array
    {
        $keywords = [];
        
        // Gaming keywords
        $gamingKeywords = [
            'gaming indonesia', 'esports indonesia', 'game mobile', 'pro player',
            'tournament gaming', 'update game', 'berita gaming', 'komunitas gaming',
            'streamer indonesia', 'content creator', 'gaming news', 'esports news'
        ];
        
        // Extract from title and content
        $text = strtolower($title . ' ' . strip_tags($content));
        
        foreach ($gamingKeywords as $keyword) {
            if (stripos($text, $keyword) !== false) {
                $keywords[] = $keyword;
            }
        }
        
        // Add game-specific keywords
        $gameNames = ['mobile legends', 'pubg', 'valorant', 'free fire', 'dota 2', 'call of duty'];
        foreach ($gameNames as $game) {
            if (stripos($text, $game) !== false) {
                $keywords[] = $game;
                $keywords[] = $game . ' indonesia';
                $keywords[] = $game . ' update';
            }
        }
        
        return array_unique($keywords);
    }

    /**
     * Create social media optimized title.
     */
    private function createSocialTitle(string $title): string
    {
        $socialPrefixes = [
            '🔥 VIRAL!', '⚡ TRENDING!', '🚨 BREAKING!', '💥 HOT NEWS!',
            '🎮 GAMING NEWS!', '🏆 ESPORTS UPDATE!'
        ];
        
        $prefix = $socialPrefixes[array_rand($socialPrefixes)];
        $cleanTitle = $this->extractMainHeading($title);
        
        return $prefix . ' ' . $cleanTitle;
    }

    /**
     * Create social media optimized description.
     */
    private function createSocialDescription(string $title, string $content): string
    {
        $keyInfo = $this->extractKeyInfo($title);
        $hashtags = array_slice($this->generateSEOHashtags($title), 0, 5);
        
        $description = "Berita gaming terbaru yang lagi viral! {$keyInfo} yang bikin heboh komunitas gaming Indonesia. ";
        $description .= "Jangan sampai ketinggalan info penting ini! ";
        $description .= implode(' ', $hashtags);
        
        return Str::limit($description, 200);
    }

    /**
     * Generate Open Graph tags for Facebook.
     */
    private function generateOpenGraphTags(string $title, string $content): array
    {
        return [
            'og:title' => $this->createSocialTitle($title),
            'og:description' => $this->createSocialDescription($title, $content),
            'og:type' => 'article',
            'og:site_name' => 'News Reynra - Gaming Indonesia',
            'article:section' => 'Gaming',
            'article:tag' => implode(', ', $this->generateSEOHashtags($title))
        ];
    }

    /**
     * Generate Twitter Card tags.
     */
    private function generateTwitterTags(string $title, string $content): array
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $this->createSocialTitle($title),
            'twitter:description' => $this->createSocialDescription($title, $content),
            'twitter:site' => '@NewsReynra'
        ];
    }

    /**
     * Create source attribution.
     */
    private function createSourceAttribution(): string
    {
        return '<hr><p><em><strong>Sumber:</strong> Artikel ini ditulis berdasarkan informasi dari berbagai sumber gaming terpercaya dan telah diolah kembali oleh tim redaksi News Reynra untuk memberikan perspektif yang relevan bagi komunitas gaming Indonesia. Konten ini dibuat untuk tujuan informasi dan edukasi.</em></p>';
    }
}

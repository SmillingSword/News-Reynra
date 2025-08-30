<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImageDownloadService
{
    public function downloadAndSaveImage($imageUrl, $articleTitle = null)
    {
        try {
            if (empty($imageUrl)) {
                return null;
            }

            // Clean and validate URL
            $imageUrl = $this->cleanImageUrl($imageUrl);
            
            if (!$this->isValidImageUrl($imageUrl)) {
                return null;
            }

            // Download image
            $response = Http::timeout(30)->get($imageUrl);
            
            if (!$response->successful()) {
                return null;
            }

            // Get image content and info
            $imageContent = $response->body();
            $contentType = $response->header('Content-Type');
            
            // Determine file extension
            $extension = $this->getExtensionFromContentType($contentType) ?: 'jpg';
            
            // Generate unique filename
            $filename = $this->generateFilename($articleTitle, $extension);
            
            // Save to storage
            $path = 'images/articles/' . $filename;
            Storage::disk('public')->put($path, $imageContent);
            
            return '/storage/' . $path;
            
        } catch (\Exception $e) {
            Log::warning("Failed to download image: {$imageUrl}. Error: " . $e->getMessage());
            return null;
        }
    }

    private function cleanImageUrl($url)
    {
        // Remove query parameters that might interfere
        $url = strtok($url, '?');
        
        // Ensure proper protocol
        if (str_starts_with($url, '//')) {
            $url = 'https:' . $url;
        }
        
        return $url;
    }

    private function isValidImageUrl($url)
    {
        // Check if URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        
        // Check if it's likely an image
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
        $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
        
        return in_array($extension, $imageExtensions) || empty($extension);
    }

    private function getExtensionFromContentType($contentType)
    {
        $mimeToExt = [
            'image/jpeg' => 'jpg',
            'image/jpg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg'
        ];

        return $mimeToExt[$contentType] ?? null;
    }

    private function generateFilename($articleTitle, $extension)
    {
        $slug = $articleTitle ? Str::slug(Str::limit($articleTitle, 50)) : 'article';
        $timestamp = now()->format('Y-m-d-H-i-s');
        $random = Str::random(8);
        
        return "{$slug}-{$timestamp}-{$random}.{$extension}";
    }

    public function downloadMultipleImages($imageUrls, $articleTitle = null)
    {
        $downloadedImages = [];
        
        foreach ($imageUrls as $imageUrl) {
            $localPath = $this->downloadAndSaveImage($imageUrl, $articleTitle);
            if ($localPath) {
                $downloadedImages[] = $localPath;
            }
        }
        
        return $downloadedImages;
    }
}

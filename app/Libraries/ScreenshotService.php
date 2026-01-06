<?php

namespace App\Libraries;

/**
 * Screenshot Service
 *
 * Captures website screenshots using thumbnail.ws API
 * Free tier: 500 screenshots/day
 */
class ScreenshotService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.thumbnail.ws/api';
    protected int $width = 1280;
    protected int $height = 720;
    protected int $delay = 3; // seconds to wait for page load
    protected string $uploadPath;

    public function __construct()
    {
        $this->apiKey = getenv('THUMBNAIL_WS_API_KEY') ?: '';
        $this->uploadPath = FCPATH . 'uploads/screenshots/';

        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    /**
     * Check if API is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Capture screenshot of a URL
     *
     * @param string $url Website URL to capture
     * @return string|null Path to saved screenshot or null on failure
     */
    public function capture(string $url): ?string
    {
        if (!$this->isConfigured()) {
            log_message('warning', 'ScreenshotService: API key not configured');
            return null;
        }

        // Validate URL
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            log_message('error', 'ScreenshotService: Invalid URL - ' . $url);
            return null;
        }

        try {
            // Build API URL
            $apiUrl = $this->buildApiUrl($url);

            // Fetch screenshot
            $imageData = $this->fetchScreenshot($apiUrl);

            if ($imageData === null) {
                return null;
            }

            // Save to file
            $filename = $this->generateFilename();
            $filepath = $this->uploadPath . $filename;

            if (file_put_contents($filepath, $imageData) === false) {
                log_message('error', 'ScreenshotService: Failed to save screenshot');
                return null;
            }

            log_message('info', 'ScreenshotService: Screenshot captured for ' . $url);

            return 'uploads/screenshots/' . $filename;

        } catch (\Exception $e) {
            log_message('error', 'ScreenshotService: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Build the thumbnail.ws API URL
     */
    protected function buildApiUrl(string $url): string
    {
        $params = http_build_query([
            'url' => $url,
            'width' => $this->width,
            'height' => $this->height,
            'delay' => $this->delay,
        ]);

        return "{$this->baseUrl}/{$this->apiKey}/thumbnail/get?{$params}";
    }

    /**
     * Fetch screenshot from API
     */
    protected function fetchScreenshot(string $apiUrl): ?string
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $apiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'AI-Showcase/1.0',
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            log_message('error', 'ScreenshotService: cURL error - ' . $error);
            return null;
        }

        if ($httpCode !== 200) {
            log_message('error', 'ScreenshotService: API returned HTTP ' . $httpCode);
            return null;
        }

        // Verify it's actually an image
        if (!$this->isValidImage($response)) {
            log_message('error', 'ScreenshotService: Invalid image response');
            return null;
        }

        return $response;
    }

    /**
     * Validate that response is a valid image
     */
    protected function isValidImage(string $data): bool
    {
        // Check for common image signatures
        $signatures = [
            "\xFF\xD8\xFF" => 'jpeg',      // JPEG
            "\x89PNG\r\n\x1A\n" => 'png',  // PNG
        ];

        foreach ($signatures as $signature => $type) {
            if (str_starts_with($data, $signature)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate unique filename
     */
    protected function generateFilename(): string
    {
        return 'screenshot_' . bin2hex(random_bytes(16)) . '.png';
    }

    /**
     * Set custom dimensions
     */
    public function setDimensions(int $width, int $height): self
    {
        $this->width = $width;
        $this->height = $height;
        return $this;
    }

    /**
     * Set page load delay
     */
    public function setDelay(int $seconds): self
    {
        $this->delay = max(0, min(10, $seconds));
        return $this;
    }
}

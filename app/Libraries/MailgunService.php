<?php

namespace App\Libraries;

class MailgunService
{
    protected string $apiKey;
    protected string $domain;
    protected string $fromEmail;
    protected string $fromName;
    protected string $baseUrl = 'https://api.mailgun.net/v3';

    public function __construct()
    {
        $this->apiKey = getenv('MAILGUN_API_KEY') ?: '';
        $this->domain = getenv('MAILGUN_DOMAIN') ?: '';
        $this->fromEmail = getenv('MAILGUN_FROM_EMAIL') ?: 'noreply@example.com';
        $this->fromName = getenv('MAILGUN_FROM_NAME') ?: 'AI Showcase';
    }

    /**
     * Check if Mailgun is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->domain);
    }

    /**
     * Send an email
     */
    public function send(string $to, string $subject, string $html, ?string $text = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Mailgun is not configured. Please set MAILGUN_API_KEY and MAILGUN_DOMAIN in .env',
            ];
        }

        $url = "{$this->baseUrl}/{$this->domain}/messages";

        $postData = [
            'from'    => "{$this->fromName} <{$this->fromEmail}>",
            'to'      => $to,
            'subject' => $subject,
            'html'    => $html,
        ];

        if ($text) {
            $postData['text'] = $text;
        }

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD        => "api:{$this->apiKey}",
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/x-www-form-urlencoded',
            ],
            CURLOPT_TIMEOUT        => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'message' => "cURL error: {$error}",
            ];
        }

        $result = json_decode($response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success' => true,
                'message' => $result['message'] ?? 'Email sent successfully',
                'id'      => $result['id'] ?? null,
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? "HTTP error: {$httpCode}",
        ];
    }

    /**
     * Send batch emails (up to 1000 recipients)
     */
    public function sendBatch(array $recipients, string $subject, string $html, ?string $text = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Mailgun is not configured',
            ];
        }

        $results = [];
        $chunks = array_chunk($recipients, 1000);

        foreach ($chunks as $chunk) {
            $to = implode(', ', $chunk);
            $result = $this->send($to, $subject, $html, $text);
            $results[] = $result;
        }

        $successCount = count(array_filter($results, fn($r) => $r['success']));

        return [
            'success' => $successCount === count($results),
            'message' => "{$successCount}/" . count($results) . " batches sent successfully",
            'results' => $results,
        ];
    }

    /**
     * Send personalized emails to multiple recipients
     */
    public function sendPersonalized(array $recipients, string $subject, callable $htmlGenerator): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Mailgun is not configured',
                'sent'    => 0,
                'failed'  => 0,
            ];
        }

        $sent = 0;
        $failed = 0;
        $errors = [];

        foreach ($recipients as $recipient) {
            $email = $recipient['email'] ?? $recipient;
            $html = $htmlGenerator($recipient);

            $result = $this->send($email, $subject, $html);

            if ($result['success']) {
                $sent++;
            } else {
                $failed++;
                $errors[] = [
                    'email'   => $email,
                    'message' => $result['message'],
                ];
            }

            // Rate limiting: Mailgun allows 300 emails/min on free tier
            usleep(200000); // 200ms delay between emails
        }

        return [
            'success' => $failed === 0,
            'message' => "Sent: {$sent}, Failed: {$failed}",
            'sent'    => $sent,
            'failed'  => $failed,
            'errors'  => $errors,
        ];
    }
}

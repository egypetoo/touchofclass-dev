<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentGenerationService
{
    protected ?string $apiKey;

    protected string $baseUrl;

    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
        $this->baseUrl = config('services.claude.base_url', 'https://api.anthropic.com/v1');
        $this->model = config('services.claude.model', 'claude-3-haiku-20240307');
    }

    public function generateText(string $prompt, array $options = []): array
    {
        if (blank($this->apiKey)) {
            return [
                'success' => false,
                'error' => __('admin_trans.claude_api_key_missing'),
            ];
        }

        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
                'anthropic-version' => '2023-06-01',
            ])->timeout(60)->post($this->baseUrl.'/messages', [
                'model' => $this->model,
                'max_tokens' => (int) ($options['max_tokens'] ?? 500),
                'temperature' => (float) ($options['temperature'] ?? 0.7),
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => ($options['system_prompt'] ?? '')."\n\n".$prompt,
                    ],
                ],
            ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'content' => trim($data['content'][0]['text'] ?? ''),
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
            ];
        } catch (\Throwable $e) {
            Log::error('Claude AI error: '.$e->getMessage());

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}

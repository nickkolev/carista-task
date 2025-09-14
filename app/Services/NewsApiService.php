<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class NewsApiService
{
    protected string $baseUrl = 'https://newsapi.org/v2/everything';
    protected int $cacheTtl = 300;

    public function search(string $keyword, int $page = 1, int $pageSize = 10, ?string $from = null, ?string $to = null): array
    {
        $cacheKey = $this->generateCacheKey($keyword, $page, $pageSize, $from, $to);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($keyword, $page, $pageSize, $from, $to) {
            return $this->makeApiRequest($keyword, $page, $pageSize, $from, $to);
        });
    }

    private function makeApiRequest(string $keyword, int $page, int $pageSize, ?string $from, ?string $to): array
    {
        $params = [
            'q'        => $keyword,
            'apiKey'   => config('services.newsapi.key'),
            'pageSize' => $pageSize,
            'page'     => $page,
            'sortBy'   => 'publishedAt',
            'language' => 'en'
        ];

        if ($from) {
            $params['from'] = $from;
        }
        if ($to) {
            $params['to'] = $to;
        }

        try {
            $response = Http::timeout(15)->retry(3, 1000)->get($this->baseUrl, $params);

            if ($response->failed()) {
                $errorData = [
                    'keyword' => $keyword,
                    'page'    => $page,
                    'from'    => $from,
                    'to'      => $to,
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'headers' => $response->headers()
                ];

                Log::error('NewsAPI request failed', $errorData);

                if ($response->status() === 401) {
                    throw new \Exception('Invalid NewsAPI key. Please check your configuration.');
                } elseif ($response->status() === 429) {
                    throw new \Exception('NewsAPI rate limit exceeded. Please try again later.');
                } elseif ($response->status() === 426) {
                    throw new \Exception('NewsAPI upgrade required. Please check your subscription.');
                } else {
                    throw new \Exception('Failed to fetch articles from NewsAPI. Status: ' . $response->status());
                }
            }

            $data = $response->json();
            
            if (!isset($data['status']) || $data['status'] !== 'ok') {
                Log::error('NewsAPI returned invalid response', [
                    'keyword' => $keyword,
                    'response' => $data
                ]);
                throw new \Exception('Invalid response from NewsAPI.');
            }

            return $data;

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('NewsAPI connection failed', [
                'keyword' => $keyword,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Unable to connect to NewsAPI. Please check your internet connection.');
        } catch (\Exception $e) {
            if (str_contains($e->getMessage(), 'NewsAPI')) {
                throw $e;
            }
            
            Log::error('Unexpected error in NewsAPI request', [
                'keyword' => $keyword,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('An unexpected error occurred while fetching news.');
        }
    }

    private function generateCacheKey(string $keyword, int $page, int $pageSize, ?string $from, ?string $to): string
    {
        return 'newsapi_' . md5($keyword . '_' . $page . '_' . $pageSize . '_' . $from . '_' . $to);
    }
}

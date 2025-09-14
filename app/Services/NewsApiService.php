<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiService
{
    protected string $baseUrl = 'https://newsapi.org/v2/everything';

    /**
     * Fetch articles from NewsAPI.
     *
     * @param string $keyword
     * @return array
     * @throws \Exception
     */
    public function search(string $keyword): array
    {
        $response = Http::timeout(10)->get($this->baseUrl, [
            'q'        => $keyword,
            'apiKey'   => config('services.newsapi.key'),
            'pageSize' => 20,
            'sortBy'   => 'publishedAt',
            'language' => 'en'
        ]);

        if ($response->failed()) {
            Log::error('NewsAPI request failed', [
                'keyword' => $keyword,
                'status'  => $response->status(),
                'body'    => $response->body()
            ]);

            throw new \Exception('Failed to fetch articles from NewsAPI.');
        }

        return $response->json();
    }
}

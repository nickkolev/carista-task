<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\NewsApiResponse;
use App\Services\NewsApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.newsapi.key' => 'test-api-key']);
    }

    /** @test */
    public function it_can_search_for_news_articles()
    {
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 100,
                'articles' => [
                    [
                        'title' => 'Test Article 1',
                        'description' => 'Test description 1',
                        'url' => 'https://example.com/article1',
                        'publishedAt' => '2025-01-15T10:00:00Z',
                        'source' => ['name' => 'Test Source'],
                        'author' => 'Test Author'
                    ],
                    [
                        'title' => 'Test Article 2',
                        'description' => 'Test description 2',
                        'url' => 'https://example.com/article2',
                        'publishedAt' => '2025-01-15T11:00:00Z',
                        'source' => ['name' => 'Test Source 2'],
                        'author' => 'Test Author 2'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post('/search', [
            'keyword' => 'test keyword'
        ]);

        $response->assertStatus(302);
        $response->assertRedirect();
    }

    /** @test */
    public function it_stores_api_responses_in_database()
    {
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 50,
                'articles' => [
                    [
                        'title' => 'Test Article',
                        'description' => 'Test description',
                        'url' => 'https://example.com/article',
                        'publishedAt' => '2025-01-15T10:00:00Z',
                        'source' => ['name' => 'Test Source'],
                        'author' => 'Test Author'
                    ]
                ]
            ], 200)
        ]);

        $response = $this->post('/search', [
            'keyword' => 'test keyword'
        ]);

        $response->assertRedirect();
        $redirectResponse = $this->get($response->headers->get('Location'));

        $this->assertDatabaseHas('newsapi_responses', [
            'keyword' => 'test keyword',
            'page' => 1,
            'page_size' => 20
        ]);

        $dbResponse = NewsApiResponse::where('keyword', 'test keyword')->first();
        $this->assertNotNull($dbResponse);
        $this->assertEquals('ok', $dbResponse->response['status']);
        $this->assertEquals(50, $dbResponse->response['totalResults']);
    }

    /** @test */
    public function it_handles_api_errors_gracefully()
    {
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'error',
                'code' => 'apiKeyInvalid',
                'message' => 'Your API key is invalid.'
            ], 401)
        ]);

        $response = $this->post('/search', [
            'keyword' => 'test keyword'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('msg');
    }

    /** @test */
    public function it_handles_rate_limit_errors()
    {
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'error',
                'code' => 'rateLimited',
                'message' => 'You have exceeded your API quota.'
            ], 429)
        ]);

        $response = $this->post('/search', [
            'keyword' => 'test keyword'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('msg');
    }

    /** @test */
    public function it_caches_api_responses()
    {
        // Mock successful API response
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 100,
                'articles' => []
            ], 200)
        ]);

        $service = new NewsApiService();
        
        // First call should make HTTP request
        $result1 = $service->search('test keyword');
        
        // Second call should use cache
        $result2 = $service->search('test keyword');
        
        // Verify only one HTTP request was made
        Http::assertSentCount(1);
        
        $this->assertEquals($result1, $result2);
    }

    /** @test */
    public function it_limits_database_records_to_20_per_keyword()
    {
        // Create 25 records for the same keyword
        for ($i = 1; $i <= 25; $i++) {
            NewsApiResponse::create([
                'keyword' => 'test keyword',
                'response' => ['status' => 'ok', 'totalResults' => $i],
                'page' => 1,
                'page_size' => 20
            ]);
        }

        // Use the storeResponse method to trigger the cleanup
        NewsApiResponse::storeResponse('test keyword', ['status' => 'ok', 'totalResults' => 26], 1, 20);

        // Verify only 20 records exist
        $count = NewsApiResponse::where('keyword', 'test keyword')->count();
        $this->assertEquals(20, $count);

        // Verify the oldest records were deleted (should be record with totalResults > 5)
        $oldestRecord = NewsApiResponse::where('keyword', 'test keyword')
            ->orderBy('created_at', 'asc')
            ->first();
        
        // The oldest record should have totalResults > 5 (since records 1-5 were deleted)
        $this->assertGreaterThan(5, $oldestRecord->response['totalResults']);
    }

    /** @test */
    public function it_supports_date_range_queries()
    {
        // Mock successful API response for date range
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 25,
                'articles' => []
            ], 200)
        ]);

        $service = new NewsApiService();
        $result = $service->search('test keyword', 1, 20, '2025-01-15', '2025-01-15');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'newsapi.org/v2/everything') &&
                   $request['q'] === 'test keyword' &&
                   $request['from'] === '2025-01-15' &&
                   $request['to'] === '2025-01-15';
        });

        $this->assertEquals('ok', $result['status']);
    }

    /** @test */
    public function it_handles_connection_timeouts()
    {
        // Mock connection timeout
        Http::fake([
            'newsapi.org/v2/everything*' => function () {
                throw new \Illuminate\Http\Client\ConnectionException('Connection timeout');
            }
        ]);

        $response = $this->post('/search', [
            'keyword' => 'test keyword'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('msg');
    }

    /** @test */
    public function it_validates_search_keyword()
    {
        $response = $this->post('/search', [
            'keyword' => ''
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('keyword');
    }

    /** @test */
    public function it_handles_pagination_correctly()
    {
        // Mock successful API response
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 100,
                'articles' => []
            ], 200)
        ]);

        $response = $this->get('/search?keyword=test&page=2');

        $response->assertStatus(200);
        $response->assertViewIs('news.results');
    }

    /** @test */
    public function it_clears_cache_when_needed()
    {
        // Mock successful API response
        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'status' => 'ok',
                'totalResults' => 100,
                'articles' => []
            ], 200)
        ]);

        $service = new NewsApiService();
        
        // Make first call
        $service->search('test keyword');
        
        // Clear cache
        Cache::flush();
        
        // Make second call - should make new HTTP request
        $service->search('test keyword');
        
        // Verify two HTTP requests were made
        Http::assertSentCount(2);
    }
}
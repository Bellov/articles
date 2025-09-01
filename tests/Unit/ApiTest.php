<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NewsApiService;
use Illuminate\Support\Facades\Http;

class ApiTest extends TestCase
{
    public function test_search_articles_returns_data_on_success()
    {
        Http::fake([
            'newsapi.org/*' => Http::response([
                'status' => 'ok',
                'articles' => [['title' => 'Test Article']]
            ], 200)
        ]);

        $service = new NewsApiService();
        $result = $service->searchArticles('Laravel');

        $this->assertNotNull($result);
        $this->assertEquals('ok', $result['status']);
        $this->assertArrayHasKey('articles', $result);
    }

    public function test_search_articles_returns_data_on_failed()
    {
        Http::fake([
            'newsapi.org/*' => Http::response([
                'status' => 'error',
                'message' => "api key is invalid"
            ], 401)
        ]);

        $service = new NewsApiService();
        $result = $service->searchArticles('test');
        $this->assertNull($result);
    }

    public function test_search_articles_returns_empty_result()
    {
        Http::fake([
            'newsapi.org/*' => Http::response([
                'status' => 'ok',
                'articles' => []
            ], 200)
        ]);

        $service = new NewsApiService();
        $result = $service->searchArticles('Nothing');

        $this->assertEquals([], $result['articles']);
    }
}

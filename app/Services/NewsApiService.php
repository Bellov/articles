<?php

namespace App\Services;

use App\Models\Article;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsApiService
{
    private $apiKey;
    private $baseUrl = 'https://newsapi.org/v2';

    private const API_TIMEOUT = 20;
    private const API_RETRY_ATTEMPTS = 2;
    private const API_RETRY_DELAY = 1000;
    private const API_ARTICLES_PER_PAGE = 20;
    private const CACHE_TTL = 3600; // we cache the keyword from searching for one hour. 

    public function __construct()
    {
        $this->apiKey = env('ARTICLES_API_KEY');

        if (empty($this->apiKey)) {
            throw new InvalidArgumentException('NewsAPI key is required ! ');
        }
    }

    public function searchArticles(string $keyword)
    {
        $cacheKey = $this->generateCacheKey($keyword);

        if (Cache::has($cacheKey)) {
            Log::info("Returning cached results for keyword: {$keyword}");
            return Cache::get($cacheKey);
        }

        try {
            $response = Http::timeout(self::API_TIMEOUT)
                ->retry(self::API_RETRY_ATTEMPTS, self::API_RETRY_DELAY)
                ->get("{$this->baseUrl}/everything", [
                    'q' => $keyword,
                    'apiKey' => $this->apiKey,
                    'language' => 'en',
                    'sortBy' => 'publishedAt',
                    'pageSize' => self::API_ARTICLES_PER_PAGE
                ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->cacheResults($cacheKey, $data, $keyword);

                return $data;
            } else {
                Log::error('NewsAPI error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }
        } catch (\Exception $e) {
            Log::error('NewsAPI exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    private function generateCacheKey(string $keyword): string
    {
        $normalizedKeyword = strtolower(trim($keyword));
        $date = now()->format('Y-m-d');
        return "news_search:{$normalizedKeyword}:{$date}";
    }

    private function cacheResults(string $cacheKey, array $data, string $keyword): void
    {
        try {
            // check for tag is exist
            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                Cache::tags(['news_search', $keyword])->put($cacheKey, $data, self::CACHE_TTL);
            } else {
                Cache::put($cacheKey, $data, self::CACHE_TTL);
            }

            Log::info("Cached results for keyword: {$keyword} with key: {$cacheKey}");
        } catch (\Exception $e) {
            Log::error('Failed to cache results', [
                'keyword' => $keyword,
                'cache_key' => $cacheKey,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function invalidateCache(string $keyword): void
    {
        try {
            $cacheKey = $this->generateCacheKey($keyword);

            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                Cache::tags(['news_search', $keyword])->flush();
            } else {
                Cache::forget($cacheKey);
            }

            Log::info("Invalidated cache for keyword: {$keyword}");
        } catch (\Exception $e) {
            Log::error('Failed to invalidate cache', [
                'keyword' => $keyword,
                'error' => $e->getMessage()
            ]);
        }
    }
    public function getCacheStats(): array
    {
        try {
            $cachedKeywords = 0;

            if (Cache::getStore() instanceof \Illuminate\Cache\TaggableStore) {
                $keys = Cache::tags(['news_search'])->get('stats', []);
                $cachedKeywords = count($keys);
            }

            return [
                'cached_keywords' => $cachedKeywords,
                'cache_driver' => config('cache.default'),
                'supports_tagging' => Cache::getStore() instanceof \Illuminate\Cache\TaggableStore
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get cache stats', ['error' => $e->getMessage()]);
            return [];
        }
    }

    public function saveSearchResult(string $keyword, array $apiResponse)
    {
        if (!isset($apiResponse['articles']) || empty($apiResponse['articles'])) {
            Log::info("No articles found for keyword: {$keyword}");
            return;
        }

        foreach ($apiResponse['articles'] as $article) {
            try {
                Article::create([
                    'keyword' => $keyword,
                    'response_json' => json_encode($article)
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to save article', [
                    'title' => $article['title'] ?? 'Unknown',
                    'error' => $e->getMessage()
                ]);
            }
        }
        Log::info("Saved " . count($apiResponse['articles']) . " articles for keyword: {$keyword}");
    }
}

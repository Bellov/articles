<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use App\Services\NewsApiService;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    private $newsApiService;
    private const PAGINATOR_LIMIT = 20;

    public function __construct(NewsApiService $newsApiService)
    {
        $this->newsApiService = $newsApiService;
    }

    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|min:2|max:100'
        ]);

        $search = $request->input('keyword');

        $cacheKey = "news_search:" . strtolower(trim($search)) . ":" . now()->format('Y-m-d');

        if (Cache::has($cacheKey)) {
            $data = Cache::get($cacheKey);
            $articlesCount = count($data['articles'] ?? []);

            return response()->json([
                'success' => true,
                'message' => "Successfully found {$articlesCount} articles for '{$search}' (now we get results from the cache)",
                'articles' => $data['articles'],
                'meta' => [
                    'keyword' => $search,
                    'total_results' => $articlesCount,
                    'search_time' => now()->format('H:i:s'),
                    'cached' => true,
                    'cache_key' => $cacheKey
                ]
            ]);
        }

        $data = $this->newsApiService->searchArticles($search);

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }

        $this->newsApiService->saveSearchResult($search, $data);

        $articlesCount = count($data['articles'] ?? []);

        return response()->json([
            'success' => true,
            'message' => "Successfully found {$articlesCount} articles for '{$search}'",
            'articles' => $data['articles'],
            'meta' => [
                'keyword' => $search,
                'total_results' => $articlesCount,
                'search_time' => now()->format('H:i:s'),
                'cached' => false,
                'cache_key' => $cacheKey
            ]
        ]);
    }

    public function getResults(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = self::PAGINATOR_LIMIT;

            $articles = Article::orderBy('created_at', 'desc')
                ->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'success' => true,
                'articles' => $articles->items(),
                'pagination' => [
                    'current_page' => $articles->currentPage(),
                    'last_page' => $articles->lastPage(),
                    'per_page' => $articles->perPage(),
                    'total' => $articles->total(),
                    'from' => $articles->firstItem(),
                    'to' => $articles->lastItem()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load articles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getChartData()
    {
        try {
            $chartData = Article::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(7))
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $labels = [];
            $data = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->format('Y-m-d');
                $labels[] = now()->subDays($i)->format('M d');

                $count = $chartData->where('date', $date)->first();
                $data[] = $count ? $count->count : 0;
            }

            return response()->json([
                'success' => true,
                'labels' => $labels,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load chart data: ' . $e->getMessage()
            ], 500);
        }
    }
}

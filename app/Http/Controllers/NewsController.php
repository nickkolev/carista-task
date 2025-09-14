<?php

namespace App\Http\Controllers;

use App\Services\NewsApiService;
use App\Models\NewsApiResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class NewsController extends Controller
{
    public function index()
    {
        return view('news.index');
    }

    public function search(Request $request, NewsApiService $newsApiService)
    {
        $request->validate([
            'keyword' => 'required|string|max:255'
        ]);

        $keyword = $request->input('keyword');
        $page = $request->input('page', 1);
        $perPage = 20;

        if ($request->isMethod('post')) {
            return redirect()->route('news.search', [
                'keyword' => $keyword,
                'page' => $page
            ]);
        }

        try {
            $response = $newsApiService->search($keyword, $page, $perPage);

            NewsApiResponse::storeResponse($keyword, $response, $page, $perPage);

            $articles = $response['articles'] ?? [];
            $totalResults = $response['totalResults'] ?? 0;

            $paginator = new LengthAwarePaginator(
                $articles,
                $totalResults,
                $perPage,
                $page,
                [
                    'path' => route('news.search'),
                    'pageName' => 'page',
                ]
            );

            $paginator->appends(['keyword' => $keyword]);
            $chartData = $this->getChartData($keyword, $newsApiService);

            return view('news.results', compact('articles', 'keyword', 'paginator', 'chartData'));

        } catch (\Exception $e) {
            return back()->withErrors([
                'msg' => $e->getMessage()
            ]);
        }
    }

    private function getChartData($keyword, NewsApiService $newsApiService)
    {
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays(6);

        $chartData = [];
        $labels = [];

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $labels[] = $date->format('M j');
            
            $dateStr = $date->format('Y-m-d');
            
            try {
                $response = $newsApiService->search($keyword, 1, 100, $dateStr, $dateStr);
                $count = $response['totalResults'] ?? 0;
                NewsApiResponse::storeResponse($keyword, $response, 1, 100, $dateStr, $dateStr);
            } catch (\Exception $e) {
                $cachedResponse = NewsApiResponse::getCachedResponse($keyword, 1, 100, $dateStr, $dateStr);
                $count = $cachedResponse ? ($cachedResponse['totalResults'] ?? 0) : 0;
            }
            
            $chartData[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $chartData
        ];
    }
}

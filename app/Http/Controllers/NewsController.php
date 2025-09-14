<?php

namespace App\Http\Controllers;

use App\Services\NewsApiService;
use App\Models\NewsApiResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Show the search form
     */
    public function index()
    {
        return view('news.index');
    }

    /**
     * Handle the search form submission
     */
    public function search(Request $request, NewsApiService $newsApiService)
    {
        // Validate user input
        $request->validate([
            'keyword' => 'required|string|max:255'
        ]);

        $keyword = $request->input('keyword');

        try {
            // Fetch articles from NewsAPI
            $response = $newsApiService->search($keyword);

            // Save raw JSON response
            NewsapiResponse::create([
                'keyword' => $keyword,
                'response' => $response
            ]);

            // Keep only the latest 20 results for this keyword
            NewsapiResponse::where('keyword', $keyword)
                ->latest()
                ->skip(20)
                ->take(PHP_INT_MAX)
                ->delete();

            $articles = $response['articles'] ?? [];

            // Pass articles to view
            return view('news.results', compact('articles', 'keyword'));

        } catch (\Exception $e) {
            // Handle errors gracefully
            return back()->withErrors([
                'msg' => $e->getMessage()
            ]);
        }
    }
}

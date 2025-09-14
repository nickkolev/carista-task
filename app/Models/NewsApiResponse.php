<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class NewsApiResponse extends Model
{
    protected $table = 'newsapi_responses';
    
    protected $fillable = ['keyword', 'response', 'page', 'page_size', 'from_date', 'to_date'];

    protected $casts = [
        'response' => 'array',
    ];

    public static function storeResponse(string $keyword, array $response, int $page = 1, int $pageSize = 20, ?string $fromDate = null, ?string $toDate = null): self
    {
        $record = self::create([
            'keyword' => $keyword,
            'response' => $response,
            'page' => $page,
            'page_size' => $pageSize,
            'from_date' => $fromDate,
            'to_date' => $toDate,
        ]);

        $recordsToDelete = self::where('keyword', $keyword)
            ->orderBy('created_at', 'desc')
            ->offset(20)
            ->limit(1000)
            ->pluck('id');
            
        if ($recordsToDelete->isNotEmpty()) {
            self::whereIn('id', $recordsToDelete)->delete();
        }

        return $record;
    }

    public static function getCachedResponse(string $keyword, int $page = 1, int $pageSize = 20, ?string $fromDate = null, ?string $toDate = null): ?array
    {
        $query = self::where('keyword', $keyword)
            ->where('page', $page)
            ->where('page_size', $pageSize);

        if ($fromDate) {
            $query->where('from_date', $fromDate);
        } else {
            $query->whereNull('from_date');
        }

        if ($toDate) {
            $query->where('to_date', $toDate);
        } else {
            $query->whereNull('to_date');
        }

        $record = $query->latest()->first();
        
        return $record ? $record->response : null;
    }

    public function scopeForKeyword(Builder $query, string $keyword): Builder
    {
        return $query->where('keyword', $keyword);
    }

    public function scopeInDateRange(Builder $query, ?string $fromDate, ?string $toDate): Builder
    {
        if ($fromDate) {
            $query->where('from_date', $fromDate);
        } else {
            $query->whereNull('from_date');
        }

        if ($toDate) {
            $query->where('to_date', $toDate);
        } else {
            $query->whereNull('to_date');
        }

        return $query;
    }
}
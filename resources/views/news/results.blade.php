@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h3 fw-bold text-dark mb-2">
            Results for "{{ $keyword }}"
        </h2>
        <p class="text-muted mb-0">
            <i class="fas fa-newspaper me-1"></i>
            {{ isset($paginator) ? $paginator->total() : count($articles) }} articles found
        </p>
    </div>
    <a href="{{ route('news.index') }}" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left me-2"></i>
        New Search
    </a>
</div>

@if(isset($chartData) && !empty($chartData) && array_sum($chartData['data']) > 0)
<div class="chart-container">
    <h4 class="fw-bold mb-3">
        <i class="fas fa-chart-line text-primary me-2"></i>
        Articles Trend - Past 7 Days
    </h4>
    <canvas id="articlesChart" width="400" height="150"></canvas>
</div>
@endif

@if(empty($articles))
    <div class="text-center py-5">
        <i class="fas fa-search fa-3x text-muted mb-3"></i>
        <h4 class="text-muted">No articles found</h4>
        <p class="text-muted">Try searching with different keywords</p>
        <a href="{{ route('news.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Back to Search
        </a>
    </div>
@else
    <div class="row">
        @foreach($articles as $article)
            <div class="col-md-6 mb-4">
                <div class="card article-card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-3">
                            {{ Str::limit($article['title'], 80) }}
                        </h5>
                        
                        @if(!empty($article['description']))
                            <p class="card-text text-muted mb-3">
                                {{ Str::limit($article['description'], 120) }}
                            </p>
                        @endif
                        
                        <div class="article-meta mb-3">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <i class="fas fa-building me-2"></i>
                                    <strong>Source:</strong> {{ $article['source']['name'] }}
                                </div>
                                <div class="col-12 mb-2">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    <strong>Published:</strong> 
                                    {{ \Carbon\Carbon::parse($article['publishedAt'])->format('M j, Y \a\t g:i A') }}
                                </div>
                                @if(!empty($article['author']))
                                    <div class="col-12">
                                        <i class="fas fa-user me-2"></i>
                                        <strong>Author:</strong> {{ Str::limit($article['author'], 30) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <a href="{{ $article['url'] }}" 
                               target="_blank" 
                               rel="noopener noreferrer"
                               class="article-link">
                                <i class="fas fa-external-link-alt me-2"></i>
                                Read Full Article
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    @if(isset($paginator) && $paginator->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $paginator->links('pagination::bootstrap-4') }}
        </div>
    @endif
@endif
@endsection

@section('scripts')
@if(isset($chartData) && !empty($chartData) && array_sum($chartData['data']) > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('articlesChart').getContext('2d');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartData['labels']),
            datasets: [{
                label: 'Articles per Day',
                data: @json($chartData['data']),
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#2563eb',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)'
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#1d4ed8'
                }
            }
        }
    });
});
</script>
@endif
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Results for "{{ $keyword }}"</h2>

    @if(empty($articles))
        <p>No articles found.</p>
    @else
        <ul class="list-group">
            @foreach($articles as $article)
                <li class="list-group-item">
                    <h5>{{ $article['title'] }}</h5>
                    <p>
                        Source: {{ $article['source']['name'] }}<br>
                        Published: {{ \Carbon\Carbon::parse($article['publishedAt'])->format('d M Y H:i') }}
                    </p>
                    <a href="{{ $article['url'] }}" target="_blank">Read full article</a>
                </li>
            @endforeach
        </ul>
    @endif
</div>
@endsection

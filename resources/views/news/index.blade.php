@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Search News</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            {{ $errors->first('msg') }}
        </div>
    @endif

    <form method="POST" action="{{ route('news.search') }}">
        @csrf
        <input type="text" name="keyword" placeholder="Enter keyword..." required>
        <button type="submit">Search</button>
    </form>
</div>
@endsection

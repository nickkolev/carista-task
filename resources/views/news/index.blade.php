@extends('layouts.app')

@section('content')
<div class="text-center mb-5">
    <h1 class="display-4 fw-bold text-dark mb-3">
        <i class="fas fa-newspaper text-primary me-3"></i>
        Carista News Search
    </h1>
    <p class="lead text-muted">Discover the latest news from around the world</p>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ $errors->first('msg') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card search-card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('news.search') }}" class="row g-3">
            @csrf
            <div class="col-md-8">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           name="keyword" 
                           class="form-control search-input border-start-0" 
                           placeholder="Enter keywords to search news..." 
                           value="{{ old('keyword') }}"
                           required>
                </div>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary search-btn w-100">
                    <i class="fas fa-search me-2"></i>
                    Search News
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

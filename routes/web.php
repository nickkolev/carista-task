<?php

use App\Http\Controllers\NewsController; 

Route::get('/', [NewsController::class, 'index'])->name('news.index');
Route::match(['get', 'post'], '/search', [NewsController::class, 'search'])->name('news.search');

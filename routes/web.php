<?php

use App\Http\Controllers\HackerNewsController;

Route::get('/hacker-news', [HackerNewsController::class, 'index']);
Route::get('/fetch-hacker-news', [HackerNewsController::class, 'fetch']);

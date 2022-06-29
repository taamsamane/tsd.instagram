<?php

use Illuminate\Support\Facades\Route;
use JohnDoe\BlogPackage\Http\Controllers\PostController;

Route::get('/tsd/instagram/callback', [PostController::class, 'index'])->name('posts.index');
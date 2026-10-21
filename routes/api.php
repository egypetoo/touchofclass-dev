<?php

use App\Http\Controllers\Api\AboutController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\LayoutController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::get('/layout', LayoutController::class)->name('layout');
    Route::get('/home', HomeController::class)->name('home');
    Route::get('/about', AboutController::class)->name('about');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/gallery-images', [MediaController::class, 'galleryImages'])->name('gallery-images');
    Route::get('/gallery-videos', [MediaController::class, 'galleryVideos'])->name('gallery-videos');
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show');
    Route::get('/contact-us', [ContactController::class, 'index'])->name('contact-us');
    Route::post('/contact-us', [ContactController::class, 'store'])->name('contact-us.store');
});

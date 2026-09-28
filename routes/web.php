<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use App\Support\Agents;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/kit', [PageController::class, 'kit'])->name('kit');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/docs/api', [PageController::class, 'docs'])->name('docs.api');

Route::get('/contact', [ContactController::class, 'create'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->middleware('throttle:10,1')->name('contact.store');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', fn () => response(Agents::robots(), 200, ['Content-Type' => 'text/plain; charset=UTF-8']))->name('robots');

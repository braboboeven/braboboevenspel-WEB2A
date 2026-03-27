<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('docs', '/docs/api')->name('docs');
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('spel', 'pages::spel')->name('spel');
    Route::livewire('docent', 'pages::docent')->name('docent');
    Route::view('spelregels', 'pages.regels')->name('regels');
    Route::view('wacht', 'pages.wacht')->name('wacht');
    Route::livewire('leaderboard', 'pages::leaderboard')->name('leaderboard');
});

require __DIR__.'/settings.php';

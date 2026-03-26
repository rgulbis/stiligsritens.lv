<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('/builder', 'pages::builder.create')->name('builder');
    Route::livewire('/showcase', 'pages::showcase.index')->name('showcase');
});

require __DIR__.'/settings.php';

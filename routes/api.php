<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'incoming'], function () {
    Route::get('/bounce', [App\Http\Controllers\UnsubscribeController::class, 'bounce'])->name('bounce.get');
    Route::post('/bounce', [App\Http\Controllers\UnsubscribeController::class, 'bounce'])->name('bounce.post');
});
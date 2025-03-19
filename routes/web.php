<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;


Route::prefix('api')->group(function () {
    Route::resource('posts', PostController::class);
});


Route::get('/{any}', function () {
    return view('posts');
})->where('any', '.*');
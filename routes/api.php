<?php

use Brucelwayne\SEO\Controllers\ScrapSellerController;
use Illuminate\Support\Facades\Route;

Route::post('external/seller/create', [ScrapSellerController::class, 'create']);
Route::post('external/seller/get-job', [ScrapSellerController::class, 'getJob']);
Route::post('external/seller/save-post', [ScrapSellerController::class, 'savePost']);
Route::post('external/seller/save-posts', [ScrapSellerController::class, 'savePosts']);
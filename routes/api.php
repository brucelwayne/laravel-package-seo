<?php

use Illuminate\Support\Facades\Route;
use Mallria\Shop\Controllers\Scrap\ScrapSellerController;

Route::post('external/seller/create', [ScrapSellerController::class, 'create']);
Route::post('external/seller/get-job', [ScrapSellerController::class, 'getJob']);
Route::post('external/seller/save-post', [ScrapSellerController::class, 'savePost']);
Route::post('external/seller/save-posts', [ScrapSellerController::class, 'savePosts']);
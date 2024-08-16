<?php

use App\Controllers\IndexController;
use App\Core\Route\Route;

Route::get('subscription-on-change-price', [IndexController::class, 'subscribe']);
Route::get('email-verify', [IndexController::class, 'emailVerify']);

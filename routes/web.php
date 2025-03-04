<?php

use Illuminate\Support\Facades\Route;
use Tarfin\Moka\Http\Controllers\MokaCallbackController;

Route::post('callback', [MokaCallbackController::class, 'handle3D'])->name('callback.handle3D');

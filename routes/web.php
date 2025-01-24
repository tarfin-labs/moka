<?php

use Illuminate\Support\Facades\Route;
use Tarfin\Moka\Http\Controllers\CallbackController;

Route::post('callback', [CallbackController::class, 'handle3D'])->name('callback.handle3D');

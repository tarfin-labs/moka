<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Tarfin\Moka\Http\Controllers\MokaCallbackController;

Route::post('moka-callback', [MokaCallbackController::class, 'handle3D'])->name('moka-callback.handle3D');

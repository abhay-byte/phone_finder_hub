<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('phones', \App\Http\Controllers\PhoneController::class)
    ->only(['index', 'show'])
    ->names([
        'index' => 'api.phones.index',
        'show' => 'api.phones.show',
    ]);

Route::get('/keep-alive', function () {
    return response('', 200);
});

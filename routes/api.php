<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthAction;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('sign-up', [AuthAction::class, 'registration']);
Route::post('sign-in', [AuthAction::class, 'login']); 
Route::get('logout', [AuthAction::class, 'logout'])->middleware(['auth:sanctum']);

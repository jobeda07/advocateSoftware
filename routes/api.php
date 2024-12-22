<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthAction;
use App\Http\Controllers\Api\CourtListAction;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('sign-up', [AuthAction::class, 'registration']);
Route::post('sign-in', [AuthAction::class, 'login']); 
Route::get('logout', [AuthAction::class, 'logout'])->middleware(['auth:sanctum']);


Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('court-list')->group(function () {
        Route::get('/', [CourtListAction::class, 'index']);
        Route::post('store', [CourtListAction::class, 'store']); 
        Route::post('update/{id}', [CourtListAction::class, 'update']);
        Route::get('delete/{id}', [CourtListAction::class, 'delete']);
    });
});

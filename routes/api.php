<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthAction;
use App\Http\Controllers\Api\CourtListAction;
use App\Http\Controllers\Api\CaseCategoryAction;
use App\Http\Controllers\Api\CaseSectionAction;
use App\Http\Controllers\Api\CaseTypeAction;
use App\Http\Controllers\Api\CaseStageAction;
use App\Http\Controllers\Api\VisitorAction;
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
    Route::prefix('case-category')->group(function () {
        Route::get('/', [CaseCategoryAction::class, 'index']);
        Route::post('store', [CaseCategoryAction::class, 'store']); 
        Route::post('update/{id}', [CaseCategoryAction::class, 'update']);
        Route::get('delete/{id}', [CaseCategoryAction::class, 'delete']);
    });
    Route::prefix('case-section')->group(function () {
        Route::get('/', [CaseSectionAction::class, 'index']);
        Route::post('store', [CaseSectionAction::class, 'store']); 
        Route::post('update/{id}', [CaseSectionAction::class, 'update']);
        Route::get('delete/{id}', [CaseSectionAction::class, 'delete']);
    });
    Route::prefix('case-type')->group(function () {
        Route::get('/', [CaseTypeAction::class, 'index']);
        Route::post('store', [CaseTypeAction::class, 'store']); 
        Route::post('update/{id}', [CaseTypeAction::class, 'update']);
        Route::get('delete/{id}', [CaseTypeAction::class, 'delete']);
    });
    Route::prefix('case-stage')->group(function () {
        Route::get('/', [CaseStageAction::class, 'index']);
        Route::post('store', [CaseStageAction::class, 'store']); 
        Route::post('update/{id}', [CaseStageAction::class, 'update']);
        Route::get('delete/{id}', [CaseStageAction::class, 'delete']);
    });
    Route::prefix('visitor')->group(function () {
        Route::get('/', [VisitorAction::class, 'index']);
        Route::post('store', [VisitorAction::class, 'store']); 
        Route::post('update/{id}', [VisitorAction::class, 'update']);
        Route::get('delete/{id}', [VisitorAction::class, 'delete']);
    });
});

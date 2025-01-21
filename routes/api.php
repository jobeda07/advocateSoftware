<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthAction;
use App\Http\Controllers\Api\CourtListAction;
use App\Http\Controllers\Api\CaseCategoryAction;
use App\Http\Controllers\Api\CaseSectionAction;
use App\Http\Controllers\Api\CaseTypeAction;
use App\Http\Controllers\Api\CaseStageAction;
use App\Http\Controllers\Api\VisitorAction;
use App\Http\Controllers\Api\ClientTypeAction;
use App\Http\Controllers\Api\ClientAction;
use App\Http\Controllers\Api\AddressAction;
use App\Http\Controllers\Api\CasesAction;
use App\Http\Controllers\Api\EmployeeAction;
use App\Http\Controllers\Api\HearingAction;
use App\Http\Controllers\Api\CaseFeeAction;
use App\Http\Controllers\Api\CaseExtraFeeAction;
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
    Route::prefix('client-type')->group(function () {
        Route::get('/', [ClientTypeAction::class, 'index']);
        Route::post('store', [ClientTypeAction::class, 'store']); 
        Route::post('update/{id}', [ClientTypeAction::class, 'update']);
        Route::get('delete/{id}', [ClientTypeAction::class, 'delete']);
    }); 
    Route::prefix('client')->group(function () {
        Route::get('/', [ClientAction::class, 'index']);
        Route::post('store', [ClientAction::class, 'store']); 
        Route::post('update/{id}', [ClientAction::class, 'update']);
        Route::get('delete/{id}', [ClientAction::class, 'delete']);
        Route::get('show/{id}', [ClientAction::class, 'show']);
    }); 
    Route::prefix('division')->group(function () {
        Route::get('/', [AddressAction::class, 'division']);
    });
    Route::prefix('district')->group(function () {
        Route::get('/{id}', [AddressAction::class, 'district']);
    });
    Route::prefix('thana')->group(function () {
        Route::get('/{id}', [AddressAction::class, 'thana']);
    });
    Route::prefix('cases')->group(function () {
        Route::get('/', [CasesAction::class, 'index']);
        Route::post('store', [CasesAction::class, 'store']); 
        Route::post('update/{id}', [CasesAction::class, 'update']);
        Route::get('delete/{id}', [CasesAction::class, 'delete']);
        Route::get('show/{id}', [CasesAction::class, 'show']);
        Route::get('delete/case-document/{id}', [CasesAction::class, 'case_document_delete']);
        Route::post('lower/store/{id}', [CasesAction::class, 'case_lower_store']);
    }); 
    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeAction::class, 'index']);
        Route::post('store', [EmployeeAction::class, 'store']); 
        Route::post('update/{id}', [EmployeeAction::class, 'update']);
        Route::get('delete/{id}', [EmployeeAction::class, 'delete']);
    }); 

    Route::prefix('hearing')->group(function () {
        Route::get('/', [HearingAction::class, 'index']);
        Route::post('store', [HearingAction::class, 'store']); 
        Route::post('update/{id}', [HearingAction::class, 'update']);
        Route::get('delete/{id}', [HearingAction::class, 'delete']);
    }); 

    Route::prefix('case-fees')->group(function () {
        Route::get('/', [CaseFeeAction::class, 'index']);
        Route::post('store', [CaseFeeAction::class, 'store']); 
        Route::post('update/{id}', [CaseFeeAction::class, 'update']);
        Route::get('delete/{id}', [CaseFeeAction::class, 'delete']);
    }); 
    Route::prefix('extra-case-fees')->group(function () {
        Route::get('/', [CaseExtraFeeAction::class, 'index']);
        Route::post('store', [CaseExtraFeeAction::class, 'store']); 
        Route::post('update/{id}', [CaseExtraFeeAction::class, 'update']);
        Route::get('delete/{id}', [CaseExtraFeeAction::class, 'delete']);
    }); 
});

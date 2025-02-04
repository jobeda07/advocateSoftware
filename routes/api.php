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
use App\Http\Controllers\Api\ExpenseAction;
use App\Http\Controllers\Api\ExpenseCategoryAction;
use App\Http\Controllers\Api\CaseTaskAction;
use App\Http\Controllers\Api\HomeAction;
use App\Http\Controllers\Api\AboutAction;
use App\Http\Controllers\Api\ServiceAction;
use App\Http\Controllers\Api\TestimonialAction;
use App\Http\Controllers\Api\ContactAction;
use App\Http\Controllers\Api\PermissionAction;
use App\Http\Controllers\Api\ToDoListAction;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//Route::post('sign-up', [AuthAction::class, 'registration']);
Route::post('login', [AuthAction::class, 'login'])->name('login'); 

Route::get('logout', [AuthAction::class, 'logout'])->middleware(['auth:sanctum']);

            // *****  frontend    ********   //
Route::get('home-section/show', [HomeAction::class, 'show']);
Route::get('about-section/show', [AboutAction::class, 'show']);
Route::get('contact-section/show', [ContactAction::class, 'show']);
Route::get('services-section/list', [ServiceAction::class, 'index']);
Route::get('testimonial-section/list', [TestimonialAction::class, 'index']);

 // *****  admin    ********   //
Route::middleware('auth:sanctum')->group(function (){
    Route::prefix('court-list')->group(function () {
        Route::get('/', [CourtListAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CourtListAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CourtListAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CourtListAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('case-category')->group(function () {
        Route::get('/', [CaseCategoryAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseCategoryAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseCategoryAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseCategoryAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('case-section')->group(function () {
        Route::get('/', [CaseSectionAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseSectionAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseSectionAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseSectionAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('case-type')->group(function () {
        Route::get('/', [CaseTypeAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseTypeAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseTypeAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseTypeAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('case-stage')->group(function () {
        Route::get('/', [CaseStageAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseStageAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseStageAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseStageAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('visitor')->group(function () {
        Route::get('/', [VisitorAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [VisitorAction::class, 'store'])->middleware('permission:visitor-create'); 
        Route::post('update/{id}', [VisitorAction::class, 'update'])->middleware('permission:visitor-edit');
        Route::get('delete/{id}', [VisitorAction::class, 'delete'])->middleware('permission:visitor-delete');
    });
    Route::prefix('client-type')->group(function () {
        Route::get('/', [ClientTypeAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ClientTypeAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [ClientTypeAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ClientTypeAction::class, 'delete'])->middleware('permission:visitor-list');
    }); 
    Route::prefix('client')->group(function () {
        Route::get('/', [ClientAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ClientAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [ClientAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ClientAction::class, 'delete'])->middleware('permission:visitor-list');
        Route::get('show/{id}', [ClientAction::class, 'show'])->middleware('permission:visitor-list');
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
        Route::get('/', [CasesAction::class, 'index'])->middleware('permission:visitor-list');
        Route::get('/all-list', [CasesAction::class, 'all_list'])->middleware('permission:visitor-list');
        Route::post('store', [CasesAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CasesAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CasesAction::class, 'delete'])->middleware('permission:visitor-list');
        Route::get('show/{id}', [CasesAction::class, 'show'])->middleware('permission:visitor-list');
        Route::get('delete/case-document/{id}', [CasesAction::class, 'case_document_delete'])->middleware('permission:visitor-list');
        Route::post('lower/store/{id}', [CasesAction::class, 'case_lower_store'])->middleware('permission:visitor-list');
    }); 
    Route::prefix('employee')->group(function () {
        Route::get('/', [EmployeeAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [EmployeeAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [EmployeeAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [EmployeeAction::class, 'delete'])->middleware('permission:visitor-list');
    }); 

    Route::prefix('hearing')->group(function () {
        Route::get('/', [HearingAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [HearingAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [HearingAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [HearingAction::class, 'delete'])->middleware('permission:visitor-list');
    }); 

    Route::prefix('case-fees')->group(function () {
        Route::get('/', [CaseFeeAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseFeeAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseFeeAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseFeeAction::class, 'delete'])->middleware('permission:visitor-list');
    }); 
    Route::prefix('extra-case-fees')->group(function () {
        Route::get('/', [CaseExtraFeeAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseExtraFeeAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseExtraFeeAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseExtraFeeAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('expense-category')->group(function () {
        Route::get('/', [ExpenseCategoryAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ExpenseCategoryAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [ExpenseCategoryAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ExpenseCategoryAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    Route::prefix('expense')->group(function () {
        Route::get('/', [ExpenseAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ExpenseAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [ExpenseAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ExpenseAction::class, 'delete'])->middleware('permission:visitor-list');
    }); 
    Route::prefix('case-task')->group(function () {
        Route::get('/', [CaseTaskAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [CaseTaskAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::post('update/{id}', [CaseTaskAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [CaseTaskAction::class, 'delete'])->middleware('permission:visitor-list');
        Route::get('progress-list/{id}', [CaseTaskAction::class, 'progress_list'])->middleware('permission:visitor-list');
        Route::post('progress-store/{id}', [CaseTaskAction::class, 'progress_store'])->middleware('permission:visitor-list');
    }); 
    Route::prefix('home-section')->group(function () {
        Route::post('update', [HomeAction::class, 'update'])->middleware('permission:visitor-list');
    });

    Route::prefix('about-section')->group(function () {
        Route::post('update', [AboutAction::class, 'update'])->middleware('permission:visitor-list');
    });

    Route::prefix('services')->group(function () {
        Route::get('/', [ServiceAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ServiceAction::class, 'store'])->middleware('permission:visitor-list');
        Route::post('update/{id}', [ServiceAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ServiceAction::class, 'delete'])->middleware('permission:visitor-list');
    });

    Route::prefix('testimonials')->group(function () {
        Route::get('/', [TestimonialAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [TestimonialAction::class, 'store'])->middleware('permission:visitor-list');
        Route::post('update/{id}', [TestimonialAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [TestimonialAction::class, 'delete'])->middleware('permission:visitor-list');
    });

    Route::prefix('contact-section')->group(function () {
        Route::post('update', [ContactAction::class, 'update'])->middleware('permission:visitor-list');
    });

    Route::prefix('todo-lists')->group(function () {
        Route::get('/', [ToDoListAction::class, 'index'])->middleware('permission:visitor-list');
        Route::post('store', [ToDoListAction::class, 'store'])->middleware('permission:visitor-list');
        Route::post('update/{id}', [ToDoListAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('show/{id}', [ToDoListAction::class, 'show'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [ToDoListAction::class, 'delete'])->middleware('permission:visitor-list');
    });
    //39660
    Route::prefix('access-control')->group(function () {
        Route::get('/role-list', [PermissionAction::class, 'index'])->middleware('permission:role.list');
        Route::get('/permission-list', [PermissionAction::class, 'permission'])->middleware('permission:visitor-list');
        Route::post('store', [PermissionAction::class, 'store'])->middleware('permission:visitor-list'); 
        Route::get('show/{id}', [PermissionAction::class, 'show'])->middleware('permission:visitor-list');
        Route::post('update/{id}', [PermissionAction::class, 'update'])->middleware('permission:visitor-list');
        Route::get('delete/{id}', [PermissionAction::class, 'delete'])->middleware('permission:visitor-list');
    });
});

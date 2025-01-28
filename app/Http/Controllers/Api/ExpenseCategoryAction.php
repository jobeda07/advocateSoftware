<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Models\ExpenseCategory;
use App\Http\Requests\ExpenseCategoryRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseCategoryAction extends Controller
{  
    public function index(){
        try {
            $expenseCategory = ExpenseCategory::orderBy('id','desc')->get();
            $expenseCategoryData = [];

            foreach ($expenseCategory as $item) {
                $expenseCategoryData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'expenseCategory' =>$expenseCategoryData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
    public function store(ExpenseCategoryRequest $request){
        $request->validate([
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $expenseCategoryData=ExpenseCategory::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'expenseCategory-data'=> $expenseCategoryData,
                'message' => 'Data Created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Somethink went wrong',
                 'status'=>500
            ]);
        }
    } 
    public function update(ExpenseCategoryRequest $request,$id){
        $request->validate([
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $expenseCategoryData=ExpenseCategory::find($id);
            if(! $expenseCategoryData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $expenseCategoryData->update([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'expenseCategory-data'=> $expenseCategoryData,
                'message' => 'Data Update successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Somethink went wrong',
                 'status'=>500
            ]);
        }
    } 

    public function delete($id){
        DB::beginTransaction();
        try{
            $expenseCategoryData=ExpenseCategory::find($id);
            if(! $expenseCategoryData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $expenseCategoryData->delete();
            DB::commit();
            return response([
                'message' => ' Data Delete successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' =>'Somethink Went Wrong',
                 'status'=>500
            ]);
        }
    }
}

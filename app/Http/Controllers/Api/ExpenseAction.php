<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Expense;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;

class ExpenseAction extends Controller
{  
    use ImageUpload;
    public function index(Request $request){
        try {

            $search=$request->query('search');
            $query=Expense::orderBy('id', 'DESC');
            if($search){
               $query->where(function ($q) use ($search){
                   $q->where("transaction_no","like","%{$search}%")
                      ->orWhere("caseId","like","%{$search}%")
                      ->orWhereHas('expense_category', function ($query) use ($search) {
                        $query->where("name", "like", "%{$search}%");
                    });
               });
            }
           $expenses = $query->paginate(50)->appends($request->query());
           if ($expenses->isEmpty()) {
               return response()->json(['data' => []], 404);
           }
           return ExpenseResource::collection($expenses)
               ->additional(['status' => 200]);


            // $expense = Expense::orderBy('id','desc')->get();
            // return response()->json(['expense_data' => ExpenseResource::collection($expense) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(expenseRequest $request)
    {   

        DB::beginTransaction();
       // try {    
            $lastExpense = Expense::orderBy('id', 'desc')->first();
            $timestamp = now()->format('Ymd');
            if ($lastExpense) {
                $lastExpenseNumber = str_replace('EX', '', $lastExpense->transaction_no);
                $newExpenseNumber = $lastExpenseNumber + 1;
                $newtransaction_no = "EX{$newExpenseNumber}";
            }else {
                $newtransaction_no = "EX{$timestamp}01";
            } 

            $expenseData = Expense::create([
                'transaction_no' => $newtransaction_no,
                'caseId' => $request->caseId,
                'expense_category_id' => $request->expense_category_id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'comment' => $request->comment,
                'created_by' => Auth::user()->id,
            ]);
            if (isset($request->voucher_image)) {
                $file = $request->voucher_image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/voucherImage/', true);
                $expenseData->voucher_image = 'uploads/images/voucherImage/' . $filename;
                $expenseData ->save();
            }
    
            DB::commit();
            
            return response([
                'case-data' => new ExpenseResource($expenseData),
                'message' => 'Data Created successfully'
            ]);
            
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json([
        //         'error' => 'Something went wrong',
        //         'status' => 500
        //     ]);
        // }
    }
    
    public function update(ExpenseRequest $request,$id){

        DB::beginTransaction();
        try{
            $expenseData=Expense::find($id);
            //dd($expenseData);
            if(! $expenseData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            if (isset($request->voucher_image)) {
                $this->deleteOne($expenseData->voucher_image);
                $file = $request->voucher_image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/voucherImage/', true);
                $expenseData->voucher_image = 'uploads/images/voucherImage/' . $filename;
            }else{
                $expenseData->voucher_image= $expenseData->voucher_image;
            }
            $expenseData->transaction_no = $expenseData->transaction_no;
            $expenseData->caseId = $request->caseId;
            $expenseData->expense_category_id = $request->expense_category_id;
            $expenseData->amount = $request->amount;
            $expenseData->payment_method = $request->payment_method;
            $expenseData->comment = $request->comment;
            $expenseData->created_by = $expenseData->created_by;
            $expenseData ->save();
            DB::commit();
            return response([
                'case-data'=> new ExpenseResource($expenseData),
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
            $expenseData=expense::find($id);
            if(! $expenseData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            
            $this->deleteOne($expenseData->voucher_image);
            $expenseData->delete();
            
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


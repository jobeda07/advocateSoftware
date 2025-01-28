<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseExtraFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseExtraFeeRequest;
use App\Http\Resources\CaseExtraFeeResource;

class CaseExtraFeeAction extends Controller
{  
    public function index(){
        try {

            $caseExtraFee = CaseExtraFee::orderBy('id','desc')->get();
            return response()->json(['caseExtraFee_data' => CaseExtraFeeResource::collection($caseExtraFee) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(CaseExtraFeeRequest $request)
    {   

        DB::beginTransaction();
        try {    
            $feeId = CaseExtraFee::orderBy('id', 'desc')->first();
            if ($feeId) {
                $lastId = $feeId->id;
                $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
                $feeId = "CETR{$id}";
            } else {
                $timestamp = now()->format('Ymd');
                $feeId = "CETR{$timestamp}01";
            } 

            $caseExtraFeeData = CaseExtraFee::create([
                'transaction_no' => $feeId,
                'caseId' => $request->caseId,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'comment' => $request->comment,
                'created_by' => auth()->user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new CaseExtraFeeResource($caseExtraFeeData),
                'message' => 'Data Created successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong',
                'status' => 500
            ]);
        }
    }
    
    public function update(CaseExtraFeeRequest $request,$id){

        DB::beginTransaction();
        try{
            $caseExtraFeeData=CaseExtraFee::find($id);
            //dd($caseExtraFeeData);
            if(! $caseExtraFeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseExtraFeeData->update([
                'transaction_no' => $caseExtraFeeData->transaction_no,
                'caseId' => $request->caseId,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'comment' => $request->comment,
                'created_by' => $caseExtraFeeData->created_by,
            ]);
            DB::commit();
            return response([
                'case-data'=> new CaseExtraFeeResource($caseExtraFeeData),
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
            $caseExtraFeeData=CaseExtraFee::find($id);
            if(! $caseExtraFeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseExtraFeeData->delete();
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

<?php

namespace App\Http\Controllers\Api;

use App\Models\CaseFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseFeeRequest;
use App\Http\Resources\CaseFeeResource;

class CaseFeeAction extends Controller
{  
    public function index(){
        try {

            $caseFee = CaseFee::orderBy('id','desc')->get();
            return response()->json(['caseFee_data' => CaseFeeResource::collection($caseFee) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(CaseFeeRequest $request)
    {   

        DB::beginTransaction();
        try {    
            $lastFee = CaseFee::orderBy('id', 'desc')->first();
            $timestamp = now()->format('Ymd');

            if ($lastFee) {
                $lastFeeNumber = str_replace('CTR', '', $lastFee->id);
                $newFeeNumber = $lastFeeNumber + 1;
                $newFeeId = "CTR{$newFeeNumber}";
            }else {
                $newFeeId = "CTR{$timestamp}01";
            }  

            $caseFeeData = CaseFee::create([
                'transaction_no' => $newFeeId,
                'caseId' => $request->caseId,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'comment' => $request->comment,
                'created_by' => Auth::user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new CaseFeeResource($caseFeeData),
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
    
    public function update(CaseFeeRequest $request,$id){

        DB::beginTransaction();
        try{
            $caseFeeData=CaseFee::find($id);
            //dd($caseFeeData);
            if(! $caseFeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseFeeData->update([
                'transaction_no' => $caseFeeData->transaction_no,
                'caseId' => $request->caseId,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'comment' => $request->comment,
                'created_by' => $caseFeeData->created_by,
            ]);
            DB::commit();
            return response([
                'case-data'=> new CaseFeeResource($caseFeeData),
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
            $caseFeeData=CaseFee::find($id);
            if(!$caseFeeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseFeeData->delete();
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

<?php

namespace App\Http\Controllers\Api;

use App\Models\CaseExtraFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseExtraFeeRequest;
use App\Http\Resources\CaseExtraFeeResource;

class CaseExtraFeeAction extends Controller
{  

    public function index(Request $request){
        try {
            $search=$request->query('search');
            $query = CaseExtraFee::orderBy('id','DESC');
            if($search){
                $query->where(function ($q) use ($search){
                    $q->where("caseId","like","%{$search}%")
                      ->orWhere("transaction_no","like","%{$search}%")
                      ->orWhereHas('caseOf', function ($caseQuery) use ($search) { 
                        // Join the caseId table first
                        $caseQuery->whereHas('clientAdd', function ($clientQuery) use ($search) {
                            // Then filter by clientAdd details
                            $clientQuery->where("name", "like", "%{$search}%")
                                        ->orWhere("phone", "like", "%{$search}%")
                                        ->orWhere("email", "like", "%{$search}%");
                        });
                    });
                });
            }
            $caseExtraFees = $query->paginate(50)->appends($request->query());
            if ($caseExtraFees->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return CaseExtraFeeResource::collection($caseExtraFees)
                ->additional(['status' => 200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e ,
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
                $lastFeeNumber = str_replace('CETR', '', $feeId->transaction_no);
                $newFeeNumber = $lastFeeNumber + 1;
                $newFeeId = "CETR{$newFeeNumber}";

            } else {
                $timestamp = now()->format('Ymd');
                $newFeeId = "CETR{$timestamp}01";
            } 

            $caseExtraFeeData = CaseExtraFee::create([
                'transaction_no' => $newFeeId,
                'caseId' => $request->caseId,
                'amount' => $request->amount,
                'payment_type' => $request->payment_type,
                'comment' => $request->comment,
                'created_by' =>  Auth::user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new CaseExtraFeeResource($caseExtraFeeData),
                'message' => 'Data Created successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => $e ,
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
                'error' => $e ,
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
                'error' => $e ,
                 'status'=>500
            ]);
        }
    } 
}

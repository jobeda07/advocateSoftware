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
    public function index(Request $request){
        try {
            $user = Auth::user();
            $search=$request->query('search');
            $query = CaseFee::orderBy('id','DESC');
            if($search){
                $query->where(function ($q) use ($search){
                    $q->where("caseId","like","%{$search}%")
                      ->orWhere("transaction_no","like","%{$search}%")
                      ->orWhereHas('caseOf', function ($caseQuery) use ($search) {
                        $caseQuery->whereHas('clientAdd', function ($clientQuery) use ($search) {
                            $clientQuery->where("name", "like", "%{$search}%")
                                        ->orWhere("phone", "like", "%{$search}%")
                                        ->orWhere("email", "like", "%{$search}%");
                        });
                    });
                });
            }
            if ($user->hasRole('superAdmin')){
                $caseFees = $query->paginate(50)->appends($request->query());
            }
            else {
                if($user->can('caseFee-list')){
                    $caseFees = $query->where('created_by', $user->id)->paginate(50)->appends($request->query());
                }
                else {
                    return response()->json([
                        'error' => 'You don’t have permission',
                        'status' => 401
                    ]);
                }
            }

            if ($caseFees->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return CaseFeeResource::collection($caseFees)
                ->additional(['status' => 200]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage() ,
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
                $lastFeeNumber = str_replace('CTR', '', $lastFee->transaction_no);
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
                'error' => 'Something went wrong: ' . $e->getMessage() ,
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
                'error' => 'Something went wrong: ' . $e->getMessage() ,
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
                'error' => 'Something went wrong: ' . $e->getMessage() ,
                 'status'=>500
            ]);
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hearing;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Requests\HearingRequest;
use App\Http\Resources\hearingResource;
use Exception;

class HearingAction extends Controller
{  
    public function index(){
        try {

            $hearing = Hearing::orderBy('id','desc')->get();
            return response()->json(['hearing_data' => hearingResource::collection($hearing) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(HearingRequest $request)
    {   

        DB::beginTransaction();
        // try {         
            $hearingData = Hearing::create([
                'caseId' => $request->caseId,
                'court_id' => $request->court_id,
                'date_time' => $request->date_time,
                'comment' => $request->comment,
                'created_by' => auth()->user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new hearingResource($hearingData),
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
    
    public function update(HearingRequest $request,$id){

        DB::beginTransaction();
        try{
            $hearingData=Hearing::find($id);
            //dd($hearingData);
            if(! $hearingData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $hearingData->update([
                'caseId' => $request->caseId,
                'court_id' => $request->court_id,
                'date_time' => $request->date_time,
                'comment' => $request->comment,
                'created_by' => $hearingData->created_by,
            ]);
            DB::commit();
            return response([
                'case-data'=> new hearingResource($hearingData),
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
            $hearingData=Hearing::find($id);
            if($hearingData){
                $hearingData->delete();
            }
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

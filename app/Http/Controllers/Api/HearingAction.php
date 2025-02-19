<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Hearing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\HearingRequest;
use App\Http\Resources\HearingResource;

class HearingAction extends Controller
{  
    public function index(Request $request){
        try {
             $search=$request->query('search');
             $query=Hearing::orderBy('id', 'DESC');
             if($search){
                $query->where(function ($q) use ($search){
                    $q->where("caseId","like","%{$search}%")
                       ->orWhere("date_time","like","%{$search}%")
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
            $hearing = $query->paginate(50)->appends($request->query());
            if ($hearing->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return HearingResource::collection($hearing)
                ->additional(['status' => 200]);
         
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
        try {         
            $hearingData = Hearing::create([
                'caseId' => $request->caseId,
                'court_id' => $request->court_id,
                'date_time' => $request->date_time,
                'court_branch' => $request->court_branch,
                'comment' => $request->comment,
                'created_by' =>  Auth::user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new HearingResource($hearingData),
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
                'case-data'=> new HearingResource($hearingData),
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
            if(!$hearingData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $hearingData->delete();
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

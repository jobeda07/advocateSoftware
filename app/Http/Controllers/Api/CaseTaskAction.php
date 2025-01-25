<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseTask;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseTaskRequest;
use App\Http\Resources\CaseTaskResource;

class CaseTaskAction extends Controller
{  
    public function index(){
        try {

            $caseTask = CaseTask::orderBy('id','desc')->get();
            return response()->json(['caseTask_data' => CaseTaskResource::collection($caseTask) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(CaseTaskRequest $request)
    {   

        DB::beginTransaction();
        try {    
        
            $caseTaskData = CaseTask::create([
                'caseId' => $request->caseId,
                'date' => $request->date,
                'priority' => $request->priority,
                'title' => $request->title,
                'details' => $request->details,
                'assign_to' => $request->assign_to,
                'created_by' => auth()->user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new CaseTaskResource($caseTaskData),
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
    
    public function update(CaseTaskRequest $request,$id){

        DB::beginTransaction();
        try{
            $caseTaskData=CaseTask::find($id);
            //dd($caseTaskData);
            if(! $caseTaskData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseTaskData->update([
                'caseId' => $request->caseId,
                'date' => $request->date,
                'priority' => $request->priority,
                'title' => $request->title,
                'details' => $request->details,
                'assign_to' => $request->assign_to,
                'created_by' => $caseTaskData->created_by,
            ]);
            DB::commit();
            return response([
                'case-data'=> new CaseTaskResource($caseTaskData),
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
            $caseTaskData=CaseTask::find($id);
            if($caseTaskData){
                $caseTaskData->delete();
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

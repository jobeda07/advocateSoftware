<?php

namespace App\Http\Controllers\Api;

use App\Models\CaseTask;
use App\Models\TaskProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseTaskRequest;
use App\Http\Resources\CaseTaskResource;
use App\Http\Resources\TaskProgressResource;

class CaseTaskAction extends Controller
{  
    public function index(Request $request){
        try {

            $search=$request->query('search');
            $query = CaseTask::orderBy('id','DESC');

            if($search){
                $query->where(function ($q) use ($search){
                    $q->where("caseId","like","%{$search}%")
                      ->orWhere("title","like","%{$search}%");
                });
            }
            $caseTasks = $query->paginate(50)->appends($request->query());
            if ($caseTasks->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return CaseTaskResource::collection($caseTasks)
                ->additional(['status' => 200]);
         
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
                'created_by' =>  Auth::user()->id,
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
            if(! $caseTaskData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseTaskData->delete();
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
    public function show($id){
        try{
            $caseTaskData=CaseTask::find($id);
            if(! $caseTaskData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseTask=[
            'caseId'=>$caseTaskData->caseId,
            'title'=>$caseTaskData->title,
            'details'=>$caseTaskData->details,
            'client_name'=>$caseTaskData->caseOf->clientAdd->name ?? '',
            'client_phone'=>$caseTaskData->caseOf->clientAdd->phone ?? '',
            'client_type'=>$caseTaskData->caseOf->caseType->name ?? '',
            'case_type'=>$caseTaskData->caseOf->caseType->name ?? '',
            'case_stage'=>$caseTaskData->caseOf->caseStage->name ?? '',
            ];
            $TaskProgress = TaskProgress::where('case_task_id',$caseTaskData->id)->orderBy('id','desc')->get();
            if(! $caseTaskData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            return response()->json([
                 'caseTask_data' =>$caseTask,
                 'taskProgress_data' => TaskProgressResource::collection($TaskProgress),
                 'status'=>200

                 ]);
            
        }catch (\Exception $e) {
            return response()->json([
                'error' =>'Somethink Went Wrong',
                 'status'=>500
            ]);
        }
    } 

    public function progress_list(Request $request,$id)
    {   

        try {

            $TaskProgress = TaskProgress::where('case_task_id',$id)->orderBy('id','desc')->paginate(50);
            return response()->json(['caseTask_data' => TaskProgressResource::collection($TaskProgress) ,'status'=>200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
    public function progress_store(Request $request ,$id)
    {  
        $request->validate([
            'progress' => 'required|max:100',
            'remarks' => 'required',
        ]); 
        $caseTask = CaseTask::find($id);
        if(!$caseTask){
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
        DB::beginTransaction();
        try {    
        
            $TaskProgress = TaskProgress::create([
                'case_task_id' => $caseTask->id,
                'progress' => $request->progress,
                'remarks' => $request->remarks,
                'created_by' =>  Auth::user()->id,
            ]);
    
            DB::commit();
            
            return response([
                'case-data' => new TaskProgressResource($TaskProgress),
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
}

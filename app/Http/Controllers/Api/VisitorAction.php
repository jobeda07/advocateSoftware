<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Http\Requests\VisitorRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\VisitorResource;
use Exception;


class VisitorAction extends Controller
{  
    public function index(){
        try {    
            $visitor = Visitor::orderBy('id','desc')->get();
            return response()->json([
                'visitor' => VisitorResource::collection($visitor),
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
    public function store(VisitorRequest $request){

        DB::beginTransaction();
        try{

            $lastVisitor = Visitor::orderBy('id', 'desc')->first();
            $timestamp = now()->format('Ymd');
            if ($lastVisitor) {
                $lastVisitorNumber = str_replace('VI', '', $lastVisitor->visitorId);
                $newVisitorNumber = $lastVisitorNumber + 1;
                $newVisitorId = "VI{$newVisitorNumber}";
            }else {
                $newVisitorId = "VI{$timestamp}01";
            }   
            $visitorData=Visitor::create([
                'visitorId'=>$newVisitorId,
                'name'=>$request->name,
                'phone'=>$request->phone,
                'case_category_id'=>$request->case_category_id,
                'case_type'=>$request->case_type,
                'priority'=>$request->priority,
                'fees'=>$request->fees,
                'reference'=>$request->reference,
                'remark'=>$request->remark,
                'created_by'=>auth()->user()->id,
            ]);
            DB::commit();
            return response([
                'visitor-data'=> new VisitorResource($visitorData),
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
    public function update(Request $request,$id){
        
        DB::beginTransaction();

        try{
            $visitorData=visitor::where('visitorId',$id)->first();
            if(!$visitorData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $visitorData->update([
                'visitorId'=>$visitorData->visitorId,
                'name'=>$request->name,
                'phone'=>$request->phone,
                'case_category_id'=>$request->case_category_id ?? $visitorData->case_category_id,
                'case_type'=>$request->case_type ?? $visitorData->case_type,
                'priority'=>$request->priority,
                'fees'=>$request->fees,
                'reference'=>$request->reference,
                'remark'=>$request->remark,
                'created_by'=>$visitorData->created_by,
            ]);
            DB::commit();
            return response([
                'visitor-data'=> new VisitorResource($visitorData),
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
            $visitorData=visitor::where('visitorId',$id)->first();
            if(!$visitorData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $visitorData->delete();
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


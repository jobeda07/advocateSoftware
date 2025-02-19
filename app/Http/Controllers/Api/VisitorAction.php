<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Visitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\VisitorRequest;
use App\Http\Resources\VisitorResource;


class VisitorAction extends Controller
{  

    public function index(Request $request){
        try {    
            $search=$request->query('search');
            $query=Visitor::orderBy('id','desc');
            if($search){
                $query->where(function ($q) use ($search){
                    $q->where("name","like","%{$search}%")
                     ->orWhere("phone","like","%{$search}%")
                     ->orWhere("visitorId","like","%{$search}%");
                });
            }
            $visitors = $query->paginate(50)->appends($request->query());
            if($visitors->isEmpty()){
                return response()->json(['data'=>[]],404);
            }
            return VisitorResource::collection($visitors);
            
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
                'created_by'=> Auth::user()->id,
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


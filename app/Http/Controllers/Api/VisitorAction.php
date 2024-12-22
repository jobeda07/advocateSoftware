<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;


class VisitorAction extends Controller
{  
    public function index(){
        try {    
            $visitor = Visitor::all();
            $visitorData = [];

            foreach ($visitor as $item) {
                $visitorData[] = [
                    //'id' => $item->id,
                    'visitorId' => $item->visitorId,
                    'name' => $item->name,
                    'phone' => $item->phone ,
                    'case_type' => $item->case_type->name ?? '',
                    'priority' => $item->priority,
                    'condition' => $item->condition,
                    'created_by' => $item->created_by,
                    'date_time' => $item->created_at->format('j F Y  g.i A'),
                ];
            }
            return response()->json([
                'visitor' =>$visitorData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string',
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'case_type' => 'required|exists:case_types,id',
            'priority' => 'required|in:Low,Medium,High',
            'condition' => 'required|in:Positive,Negative',
        ]);
        DB::beginTransaction();
        // try{

        $visitor = Visitor::orderBy('id', 'desc')->first();
        if($visitor){
            $lastId = $visitor->id;
            $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
            $visitorId = $id;
        }else{
            $timestamp = now()->format('Ymd');
            $visitorId = "{$timestamp}01";
        }
            $visitorData=Visitor::create([
                'visitorId'=>$visitorId,
                'name'=>$request->name,
                'phone'=>$request->phone,
                'case_type'=>$request->case_type,
                'priority'=>$request->priority,
                'condition'=>$request->condition,
                'created_by'=>auth()->user()->id,
            ]);
            DB::commit();
            return response([
                'visitor-data'=> $visitorData,
                'message' => 'Data Created successfully'
            ]);
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json([
        //         'error' =>'Somethink went wrong',
        //          'status'=>500
        //     ]);
        // }
    } 
    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required|string',
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'case_type' => 'required|exists:case_types,id',
            'priority' => 'required|in:Low,Medium,High',
            'condition' => 'required|in:Positive,Negative',
        ]);
        DB::beginTransaction();
        // try{
            $visitorData=visitor::find($id);
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
                'case_type'=>$request->case_type,
                'priority'=>$request->priority,
                'condition'=>$request->condition,
                'created_by'=>$visitorData->created_by,
            ]);
            DB::commit();
            return response([
                'visitor-data'=> $visitorData,
                'message' => 'Data Update successfully'
            ]);
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     return response()->json([
        //         'error' =>'Somethink went wrong',
        //          'status'=>500
        //     ]);
        // }
    } 

    public function delete($id){
        DB::beginTransaction();
        try{
            $visitorData=visitor::find($id);
            if($visitorData){
                $visitorData->delete();
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


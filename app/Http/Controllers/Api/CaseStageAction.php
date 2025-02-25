<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CaseStageAction extends Controller
{  
    public function index(){
        try {
            $caseStage = CaseStage::orderBy('id','desc')->paginate(50);
            $caseStageData = [];

            foreach ($caseStage as $item) {
                $caseStageData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'caseStage' =>$caseStageData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $caseStageData=CaseStage::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'caseStage-data'=> $caseStageData,
                'message' => 'Data Created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    } 
    public function update(Request $request,$id){
        $request->validate([
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $caseStageData=CaseStage::find($id);
            if(! $caseStageData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseStageData->update([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'caseStage-data'=> $caseStageData,
                'message' => 'Data Update successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    } 

    public function delete($id){
        DB::beginTransaction();
        try{
            $caseStageData=CaseStage::find($id);
            if(!$caseStageData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseStageData->delete();
            DB::commit();
            return response([
                'message' => ' Data Delete successfully'
            ]);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage(),
                 'status'=>500
            ]);
        }
    }
}

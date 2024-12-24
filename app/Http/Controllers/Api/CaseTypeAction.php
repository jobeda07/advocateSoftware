<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CaseTypeAction extends Controller
{  
    public function index(){
        try {
            $caseType = CaseType::orderBy('id','desc')->get();
            $caseTypeData = [];

            foreach ($caseType as $item) {
                $caseTypeData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'caseType' =>$caseTypeData,
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
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $caseTypeData=CaseType::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'caseType-data'=> $caseTypeData,
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
        $request->validate([
            'name' => 'required|string|max:150',
        ]);
        DB::beginTransaction();
        try{

            $caseTypeData=CaseType::find($id);
            if(! $caseTypeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseTypeData->update([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'caseType-data'=> $caseTypeData,
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
            $caseTypeData=CaseType::find($id);
            if($caseTypeData){
                $caseTypeData->delete();
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CaseCategoryAction extends Controller
{  
    public function index(){
        try {
            $caseCategory = CaseCategory::orderBy('id','desc')->paginate(50);
            $caseCategoryData = [];

            foreach ($caseCategory as $item) {
                $caseCategoryData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'caseCategory' =>$caseCategoryData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
               'error' => 'Something went wrong: ' . $e->getMessage() ,
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

            $caseCategoryData=caseCategory::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'caseCategory-data'=> $caseCategoryData,
                'message' => 'Data Created successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
               'error' => 'Something went wrong: ' . $e->getMessage() ,
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

            $caseCategoryData=caseCategory::find($id);
            $caseCategoryData->update([
                'name'=>ucfirst($request->name)
            ]);
            if(!$caseCategoryData){
                return response()->json([
                   'error' => 'Something went wrong: ' . $e->getMessage() ,
                     'status'=>500
                ]);
            }
            DB::commit();
            return response([
                'caseCategory-data'=> $caseCategoryData,
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
            $caseCategoryData=caseCategory::find($id);
            if($caseCategoryData){
                $caseCategoryData->delete();
            }
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

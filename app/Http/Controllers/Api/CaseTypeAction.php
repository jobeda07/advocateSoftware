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
            // $caseType = CaseType::orderBy('id','desc')->get();
            // $caseTypeData = [];

            // foreach ($caseType as $item) {
            //     $caseTypeData[] = [
            //         'id' => $item->id,
            //         'name' => $item->name,
            //         'case_category'=>$item->case_category->name ?? ''
            //     ];
            // }
            $caseType = CaseType::orderBy('id', 'desc')->get();
            $caseTypeData = [];

            $groupedByCategory = $caseType->groupBy(function ($item) {
                return $item->case_category->name ?? 'Uncategorized'; 
            });

            foreach ($groupedByCategory as $category => $items) {
                $caseTypeData[] = [
                    'case_category' => $category,
                    'case_types' => $items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                        ];
                    })->toArray(),
                ];
            }


            return response()->json([
                'caseTypeList' =>$caseTypeData,
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
            'case_category_id' => 'required|exists:case_categories,id',
        ]);
        DB::beginTransaction();
        try{

            $caseTypeData=CaseType::create([
                'name'=>ucfirst($request->name),
                'case_category_id'=>$request->case_category_id
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
                'name'=>ucfirst($request->name),
                'case_category_id'=>$request->case_category_id
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
            if(! $caseTypeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseTypeData->delete();
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

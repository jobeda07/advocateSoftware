<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CaseSectionAction extends Controller
{  
    public function index(){
        try {
            $caseSection = CaseSection::orderBy('id','desc')->get();
            $caseSectionData = [];

            foreach ($caseSection as $item) {
                $caseSectionData[] = [
                    'id' => $item->id,
                    'section_code' => $item->section_code,
                    'section_details' => $item->section_details ,
                    'case_category' => $item->case_category->name ?? 'N/A',
                ];
            }
            return response()->json([
                'caseSection' =>$caseSectionData,
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
            'section_code' => 'required|integer',
            'section_details' => 'required',
            'case_category_id' => 'required|exists:case_categories,id',
        ]);
        DB::beginTransaction();
        try{
            $caseSectionData=CaseSection::create([
                'section_code'=>$request->section_code,
                'section_details'=>$request->section_details,
                'case_category_id'=>$request->case_category_id,
            ]);
            DB::commit();
            return response([
                'caseSection-data'=> $caseSectionData,
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
            'section_code' => 'required|integer',
            'section_details' => 'required',
            'case_category_id' => 'required',
        ]);
        DB::beginTransaction();
        try{
            $caseSectionData=CaseSection::find($id);
            if(!$caseSectionData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseSectionData->update([
                'section_code'=>$request->section_code,
                'section_details'=>$request->section_details,
                'case_category_id'=>$request->case_category_id,
            ]);
            DB::commit();
            return response([
                'caseSection-data'=> $caseSectionData,
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
            $caseSectionData=CaseSection::find($id);
            if($caseSectionData){
                $caseSectionData->delete();
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

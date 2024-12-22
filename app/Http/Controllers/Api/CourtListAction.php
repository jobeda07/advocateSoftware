<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourtList;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CourtListAction extends Controller
{
    
    public function index(){
        try {
            $courtList = CourtList::all();
            $courtListData = [];

            foreach ($courtList as $item) {
                $courtListData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'courtList' =>$courtListData,
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

            $courtdata=CourtList::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'Court-data'=> $courtdata,
                'message' => 'Court Created successfully'
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

            $courtdata=CourtList::find($id);
            $courtdata->update([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'Court-data'=> $courtdata,
                'message' => 'Court Update successfully'
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
            $courtdata=CourtList::find($id);
            if($courtdata){
                $courtdata->delete();
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
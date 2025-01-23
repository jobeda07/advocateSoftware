<?php

Namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClientType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClientTypeAction extends Controller
{  
    public function index(){
        try {
            $clientType = ClientType::orderBy('id','desc')->get();
            $clientTypeData = [];

            foreach ($clientType as $item) {
                $clientTypeData[] = [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            }
            return response()->json([
                'clientType' =>$clientTypeData,
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

            $clientTypeData=ClientType::create([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'clientType-data'=> $clientTypeData,
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

            $clientTypeData=ClientType::find($id);
            if(! $clientTypeData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $clientTypeData->update([
                'name'=>ucfirst($request->name)
            ]);
            DB::commit();
            return response([
                'clientType-data'=> $clientTypeData,
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
            $clientTypeData=ClientType::find($id);
            if($clientTypeData){
                $clientTypeData->delete();
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

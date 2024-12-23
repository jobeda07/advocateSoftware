<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClientAction extends Controller
{  
    public function index(){
        try {
            $client = Client::all();
            $clientData = [];

            foreach ($client as $item) {
                $clientData[] = [
                    'id' => $item->id,
                    'name' => $item->name,
                    'phone' => $item->phone,
                    'email' => $item->email ?? '',
                    'fathers_name' => $item->fathers_name,
                    'alternative_phone' => $item->alternative_phone,
                    'profession' => $item->profession,
                    'division_id' => $item->division_id,
                    'district_id' => $item->district_id,
                    'thana_id' => $item->thana_id,
                    'address' => $item->address,
                ];
            }
            return response()->json([
                'client' =>$clientData,
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
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'alternative_phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'email'=>'nullable|email',
            'fathers_name' => 'required|string|max:150',
            'profession' => 'required|string|max:150',
            'division_id' => 'required|exists:divisions,id',
            'district_id' => 'required|exists:districts,id',
            'thana_id' => 'required|exists:thanas,id',
            'address' => 'required|string|max:180',
        ]);
        DB::beginTransaction();
        try{
            $client = Client::orderBy('id', 'desc')->first();
            if($client){
                $lastId = $client->id;
                $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
                $clientId = $id;
            }else{
                $timestamp = now()->format('Ymd');
                $clientId = "{$timestamp}01";
            }

            $clientData=Client::create([
                'clientId'=>$clientId,
                'name'=>ucfirst($request->name),
                'phone' => $request->phone,
                'email' => $request->email ?? '',
                'fathers_name' => ucfirst($request->fathers_name),
                'alternative_phone' => $request->alternative_phone,
                'profession' => $request->profession,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'thana_id' => $request->thana_id,
                'address' => $request->address,
            ]);
            DB::commit();
            return response([
                'client-data'=> $clientData,
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
            'phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'alternative_phone' =>['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'email'=>'nullable|email',
            'fathers_name' => 'required|string|max:150',
            'profession' => 'required|string|max:150',
            'division_id' => 'required',
            'district_id' => 'required',
            'thana_id' => 'required',
            'address' => 'required|string|max:180',
        ]);
        DB::beginTransaction();
        try{

            $clientData=Client::find($id);
            if(! $clientData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $clientData->update([
                'clientId'=>$clientData->clientId,
                'name'=>ucfirst($request->name),
                'phone' => $request->phone,
                'email' => $request->email ?? '',
                'fathers_name' => ucfirst($request->fathers_name),
                'alternative_phone' => $request->alternative_phone,
                'profession' => $request->profession,
                'division_id' => $request->division_id,
                'district_id' => $request->district_id,
                'thana_id' => $request->thana_id,
                'address' => $request->address,
            ]);
            DB::commit();
            return response([
                'client-data'=> $clientData,
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
            $clientData=Client::find($id);
            if($clientData){
                $clientData->delete();
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

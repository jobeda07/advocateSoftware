<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\CourtCase;
use App\Models\CaseSection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ClientAction extends Controller
{  
    public function index(){
        try {
            $client = Client::orderBy('id','desc')->get();
            $clientData = [];

            foreach ($client as $item) {
                $clientData[] = [
                    'id' => $item->id,
                    'clientId' => $item->clientId ?? '',
                    'name' => $item->name ?? '',
                    'phone' => $item->phone ?? '',
                    'email' => $item->email ?? '',
                    'fathers_name' => $item->fathers_name ?? '',
                    'alternative_phone' => $item->alternative_phone ?? '',
                    'profession' => $item->profession ?? '',
                    'division_id' => $item->division_id?? '',
                    'district_id' => $item->district_id ?? '',
                    'thana_id' => $item->thana_id ?? '',
                    'address' => $item->address ?? '',
                    'reference' => $item->reference ?? '',
                    'created_by' => $item->createdBy->name ?? '',
                    'create_date_time' => $item->created_at->format('j F Y  g.i A'),
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
            'reference' => 'required|string|max:500',
        ]);
        DB::beginTransaction();
        try{
            $client = Client::orderBy('id', 'desc')->first();
            if($client){
                $lastId = $client->id;
                $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
                $clientId ="CL$id";
            }else{
                $timestamp = now()->format('Ymd');
                $clientId = "CL{$timestamp}01";
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
                'reference' => $request->reference,
                'created_by'=>auth()->user()->id,
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
            'reference' => 'required|string|max:500',
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
                'reference' => $request->reference,
                'created_by'=>$clientData->created_by,
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
    public function show($id){
       // try{
            $clientData=Client::find($id);
            if(!$clientData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $clientDataShow[] = [
                'id' => $clientData->id,
                'clientId' => $clientData->clientId ?? '',
                'name' => $clientData->name ?? '',
                'phone' => $clientData->phone ?? '',
                'email' => $clientData->email ?? '',
                'fathers_name' => $clientData->fathers_name ?? '',
                'alternative_phone' => $clientData->alternative_phone ?? '',
                'profession' => $clientData->profession ?? '',
                'division_id' => $clientData->division_id?? '',
                'district_id' => $clientData->district_id ?? '',
                'thana_id' => $clientData->thana_id ?? '',
                'address' => $clientData->address ?? '',
                'reference' => $clientData->reference ?? '',
                'created_by' => $clientData->createdBy->name ?? '',
                'create_date_time' => $clientData->created_at->format('j F Y  g.i A'),
            ];
            $cases = CourtCase::where('clientId',$clientData->id)->orderBy('id','desc')->get();
            $caseData = [];
            foreach ($cases as $case){
                $caseSec=explode(',',$case->case_section);
                $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
                $caseData[] = [
                    'id' => $case->id,
                    'caseID' => $case->caseID,
                    'case_section' => $caseSections->toArray(),
                    'case_type' => $case->caseType->name,
                    'case_stage' => $case->caseStage->name,
                    'fees' => $case->fees ?? '',
                    'court' => $case->courtAdd->name,
                    'create_date_time' => $case->created_at->format('j F Y  g.i A'),
                ];
            }
           
            return response()->json([
                'client' =>$clientData,
                'case_Data' =>$caseData,
                 'status'=>200
            ]);
        // }catch (\Exception $e) {
        //     return response()->json([
        //         'error' =>'Somethink Went Wrong',
        //          'status'=>500
        //     ]);
        // }
    }
}

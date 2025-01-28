<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\CourtCase;
use App\Models\CaseSection;
use App\Models\CaseDocument;
use App\Http\Resources\ClientResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\ClientRequest;
use Exception;

class ClientAction extends Controller
{  
    public function index(){
        try {
            $client = Client::orderBy('id','desc')->get();
            $clientData = [];

            foreach ($client as $item) {
                $cases=CourtCase::where('clientId',$item->clientId)->count();
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
                    'cases' => $cases ?? '',
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
    public function store(ClientRequest $request){
        DB::beginTransaction();
        try{
            $lastClient = Client::orderBy('id', 'desc')->first();
            $timestamp = now()->format('Ymd');
            if($lastClient){
                $lastClientNumber = str_replace('CL', '', $lastClient->clientId);
                $newClientNumber = $lastClientNumber + 1;
                $newClientId = "CL{$newClientNumber}";
            }else{
                $timestamp = now()->format('Ymd');
                $newClientId = "CL{$timestamp}01";
            }
            $clientData=Client::create([
                'clientId'=>$newClientId,
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
    public function update(ClientRequest $request,$id){

        DB::beginTransaction();
        try{

            $clientData=Client::where('clientId',$id)->first();
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
            $clientData=Client::where('clientId',$id)->first();
            if(! $clientData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $cases = CourtCase::where('clientId',$clientData->clientId)->get();
            if($cases){
                foreach($cases as $caseData){
                    $casedocument=CaseDocument::where('courtCase_id',$caseData->id)->get();
                        if($casedocument){
                            foreach($casedocument as $item){
                                if ($item->case_image) {
                                    $this->deleteOne($item->case_image);
                                }
                                if ($item->case_pdf) {
                                    $removefile = public_path($item->case_pdf);
                                    File::delete($removefile);
                                }
                                $item->delete();
                            }
                        }
                    $caseData->delete();
                }
            }
            $clientData->delete();
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
       try{
            $clientData=Client::where('clientId',$id)->first();
            if(!$clientData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $cases = CourtCase::where('clientId',$clientData->clientId)->orderBy('id','desc')->get();
            $caseData = [];
            foreach ($cases as $case){
                $caseSec=explode(',',$case->case_section);
                $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
                $caseData[] = [
                   // 'id' => $case->id,
                    'caseId' => $case->caseId,
                    'case_section' => $caseSections->toArray(),
                    'client_type' => $case->clientType->name ?? '',
                    'case_type' => $case->caseType->name ?? '',
                    'case_category' => $case->caseCategory->name ?? '',
                    'case_stage' => $case->caseStage->name ?? '',
                    'fees' => $case->fees ?? '',
                    //'court' => $case->courtAdd->name ?? '',
                    'create_date_time' => $case->created_at->format('j F Y  g.i A'),
                ];
            }
           
            return response()->json([
                'client' =>new ClientResource($clientData),
                'case_Data' =>$caseData,
                 'status'=>200
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'error' =>'Somethink Went Wrong',
                 'status'=>500
            ]);
        }
    }
}

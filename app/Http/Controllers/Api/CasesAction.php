<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourtCase;
use App\Models\CaseSection;
use App\Models\CaseDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;
use App\Traits\ImageUpload;
use App\Http\Resources\CourtCaseResource;

class CasesAction extends Controller
{  
    use ImageUpload;
    public function index(){
        try {
            $case = CourtCase::orderBy('id','desc')->get();
            $caseData = [];
            foreach ($case as $item) {
                $caseSec=explode(',',$item->case_section);
                $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
                $caseData[] = [
                    'id' => $item->id,
                    'clientId' => $item->client->clientId ?? '',
                    'case_section' => $caseSections->toArray(),
                    'case_type' => $item->caseType->name ?? '',
                    'case_stage' => $item->caseStage->name ?? '',
                    'client_type' => $item->clientType->title ?? '',
                    'court' => $item->courtAdd->name,
                    'create_date_time' => $item->created_at->format('j F Y  g.i A'),
                ];
            }
            return response()->json([
                'case' =>$caseData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(CourtCaseRequest $request)
    {
       
    
        DB::beginTransaction();
        try {
            // Create case data
            $witnesses=[];
            if($request->witnesses){
            $witnesses = array_map(function ($witness) {
                return [
                    'name' => $witness['name'],
                    'phone' => $witness['phone'],
                ];
            }, $request->witnesses);
          }

          $caseId = CourtCase::orderBy('id', 'desc')->first();
            if ($caseId) {
                $lastId = $caseId->id;
                $id = str_pad($lastId + 1, 7, 0, STR_PAD_LEFT);
                $caseId = "VI{$id}";
            } else {
                $timestamp = now()->format('Ymd');
                $caseId = "VI{$timestamp}01";
            } 
            $caseData = CourtCase::create([
                'caseId' => $caseId,
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_category' => $request->case_category,
                'case_section' =>$request->case_section,
                'case_stage' => $request->case_stage,
                'court' => $request->court,
                'court_branch' => $request->court_branch,
                'fees' => $request->fees,
                'comments' => $request->comments,
                'opposition_phone' => $request->opposition_phone,
                'opposition_name' => ucfirst($request->opposition_name),
                'witnesses' => $witnesses,
            ]);
    
            $createdDocuments = [];
            
            if ($request->has('case_doc_name') && is_array($request->case_doc_name)) {
                foreach ($request->case_doc_name as $index => $name) {
                    $data = [
                        'courtCase_id' => $caseData->id,
                        'case_doc_name' => $name,
                    ];
    
                    if (isset($request->case_image[$index])) {
                        $file = $request->case_image[$index];
                        $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/caseImage/', true);
                        $data['case_image'] = 'uploads/images/caseImage/' . $filename;
                    }
    
                    if (isset($request->case_pdf[$index])) {
                        $file = $request->case_pdf[$index];
                        $pdfName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/pdf/casePDF/'), $pdfName);
                        $data['case_pdf'] = 'uploads/pdf/casePDF/' . $pdfName;
                    }
    
                    $createdDocuments[] = CaseDocument::create($data);
                }
            }
    
            DB::commit();
            
            return response([
                'case-data' => new CourtCaseResource($caseData),
                'message' => 'Data Created successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'error' => 'Something went wrong',
                'status' => 500
            ]);
        }
    }
    
    public function update(CourtCaseRequest $request,$id){
        
        DB::beginTransaction();
        try{
            $caseData=CourtCase::find($id);
            //dd($caseData);
            if(! $caseData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            if($request->witnesses){
                $witnesses = array_map(function ($witness) {
                    return [
                        'name' => $witness['name'],
                        'phone' => $witness['phone'],
                    ];
                }, $request->witnesses);
            }
            $caseData->update([
                'caseID' => $caseData->caseID,
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_category' => $request->case_category,
                'case_section' => $request->case_section,
                'case_stage' => $request->case_stage,
                'witnesses' => $witnesses?? $caseData->witnesses,
                'court' => $request->court,
                'fees' => $request->fees,
                'comments' => $request->comments,
                'opposition_phone' => $request->opposition_phone,
                'opposition_name' => ucfirst($request->opposition_name),
            ]);
            $createdDocuments = [];
            
            if ($request->has('case_doc_name') && is_array($request->case_doc_name)) {
                foreach ($request->case_doc_name as $index => $name) {
                    $data = [
                        'courtCase_id' => $caseData->id,
                        'case_doc_name' => $name,
                    ];
    
                    if (isset($request->case_image[$index])) {
                        $file = $request->case_image[$index];
                        $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/caseImage/', true);
                        $data['case_image'] = 'uploads/images/caseImage/' . $filename;
                    }
    
                    if (isset($request->case_pdf[$index])) {
                        $file = $request->case_pdf[$index];
                        $pdfName = time() . '_' . $index . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('uploads/pdf/casePDF/'), $pdfName);
                        $data['case_pdf'] = 'uploads/pdf/casePDF/' . $pdfName;
                    }
    
                    $createdDocuments[] = CaseDocument::create($data);
                }
            }
            DB::commit();
            return response([
                'case-data' => new CourtCaseResource($caseData),
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
            $caseData=CourtCase::find($id);
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
            if($caseData){
                $caseData->delete();
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
    public function case_document_delete($id){
        DB::beginTransaction();
        try{
            $casedocument=CaseDocument::find($id);
            if($casedocument){
                if ($casedocument->case_image) {
                    $this->deleteOne($casedocument->case_image);
                }
                if ($casedocument->case_pdf) {
                    $removefile = public_path($casedocument->case_pdf);
                    File::delete($removefile);
                }
                $casedocument->delete();
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
        try {
            $case = CourtCase::find($id);
            $caseData =new CourtCaseResource($case);
            
            return response()->json([
                 'case' =>$caseData,
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CourtCase;
use App\Models\CaseDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Exception;
use App\Traits\ImageUpload;

class CasesAction extends Controller
{  
    use ImageUpload;
    public function index(){
        // try {
            $case = CourtCase::all();
            $caseData = [];
            foreach ($case as $item) {
                $caseData[] = [
                    'id' => $item->id,
                    'clientId' => $item->client->name,
                    'case_section' => $item->caseSection->section_code,
                    'case_type' => $item->caseType->name,
                    'case_stage' => $item->caseStage->name,
                    'client_type' => $item->clientType->title,
                    'court' => $item->courtAdd->name,
                    'opposition_name' => $item->opposition_name,
                    'opposition_phone' => $item->opposition_phone,
                    'comments' => $item->comments,
                    'case_documents' => $item->caseDocument->map(function ($doc) {
                        return [
                            'id' => $doc->id,
                            'name' => $doc->name,
                            'case_image' =>$doc->case_image ? env('APP_URL') . "/" .$doc->case_image : '',
                            'case_pdf' => $doc->case_pdf ? env('APP_URL') . "/" .$doc->case_pdf : '',
                        ];
                    }),
                ];
            }
            return response()->json([
                'case' =>$caseData,
                 'status'=>200
            ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'error' =>'data not found',
        //          'status'=>500
        //     ]);
        // }
    }
    public function store(Request $request)
    {
        $request->validate([
            'clientId' => 'required|integer|exists:clients,id',
            'client_type' =>'required|integer|exists:client_types,id',
            'case_type' => 'required|integer|exists:case_types,id',
            'case_section' =>'required|integer|exists:case_sections,id',
            'case_stage' =>'required|integer|exists:case_stages,id',
            'court' => 'required|integer|exists:court_lists,id',
            'comments' => 'required',
            'opposition_phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'opposition_name' => 'required|string|max:150',
            'case_doc_name' => 'nullable|array',
            'case_doc_name.*' => 'nullable|string',
            'case_image.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'case_pdf.*' => 'nullable|mimes:pdf|max:2000',
        ]);
    
        DB::beginTransaction();
        try {
            // Create case data
            $caseData = CourtCase::create([
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_section' => $request->case_section,
                'case_stage' => $request->case_stage,
                'court' => $request->court,
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
                'case-data' => $caseData,
                'case-documents' => $createdDocuments,
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
    
    public function update(Request $request,$id){
        $request->validate([
            'clientId' => 'required|integer|exists:clients,id',
            'client_type' =>'required|integer|exists:client_types,id',
            'case_type' => 'required|integer|exists:case_types,id',
            'case_section' =>'required|integer|exists:case_sections,id',
            'case_stage' =>'required|integer|exists:case_stages,id',
            'court' => 'required|integer|exists:court_lists,id',
            'comments' => 'required',
            'opposition_phone' => ['required', 'regex:/(\+){0,1}(88){0,1}01(3|4|5|6|7|8|9)(\d){8}/', 'digits:11'],
            'opposition_name' => 'required|string|max:150',
            'case_doc_name' => 'nullable|array',
            'case_doc_name.*' => 'nullable|string',
            'case_image.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'case_pdf.*' => 'nullable|mimes:pdf|max:2000',
        ]);
        DB::beginTransaction();
        try{
            $caseData=CourtCase::find($id);
            if(! $caseData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseData->update([
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_section' => $request->case_section,
                'case_stage' => $request->case_stage,
                'court' => $request->court,
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
                'case-data'=> $caseData,
                'case-documents' => $createdDocuments,
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
}
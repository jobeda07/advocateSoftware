<?php

namespace App\Http\Controllers\Api;

use DateTime;
use Exception;
use App\Models\User;
use App\Models\CaseFee;
use App\Models\Expense;
use App\Models\Hearing;
use App\Models\CourtCase;
use App\Models\CaseHistory;
use App\Models\CaseSection;
use App\Traits\ImageUpload;
use App\Models\CaseDocument;
use App\Models\CaseExtraFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CourtCaseRequest;
use App\Http\Resources\CaseFeeResource;
use App\Http\Resources\ExpenseResource;
use App\Http\Resources\CourtCaseResource;
use App\Http\Resources\CaseHistoryResource;
use App\Http\Resources\CaseExtraFeeResource;
use App\Http\Resources\IndexCourtCaseResource;

class CasesAction extends Controller
{  
    use ImageUpload;
   
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $query = CourtCase::orderByDesc('id');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where("caseId", "like", "%{$search}%")
                    ->orWhere("priority", "like", "%{$search}%")
                    ->orWhere("clientId", "like", "%{$search}%")
                    ->orWhereHas('clientAdd', function ($query) use ($search) {
                        $query->where("name", "like", "%{$search}%")
                            ->orWhere("phone", "like", "%{$search}%")
                            ->orWhere("email", "like", "%{$search}%");
                    });
                });
            }

            $cases = $query->paginate(1)->appends($request->query());

            if ($cases->isEmpty()) {
                return response()->json(['data' => []], 404);
            }

            return IndexCourtCaseResource::collection($cases)
                ->additional(['status' => 200]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Data not found',
                'status' => 500
            ]);
        }
    }
    
     public function all_list(){
       try {
            $case = CourtCase::orderBy('id','desc')->get();
            $caseData = [];
            foreach ($case as $item) {
                $caseData[] = [
                    'caseId' => $item->caseId,
                    'client_name' => $item->clientAdd->name ?? '',
                    'client_phone' => $item->clientAdd->phone ?? '',
                ];
            }
            return response()->json([
                'all_case_list' =>$caseData,
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

            $lastCase = CourtCase::orderBy('id', 'desc')->first();
            $timestamp = now()->format('Ymd');
            if ($lastCase) {
                $lastCaseNumber = str_replace('CA', '', $lastCase->caseId);
                $newCaseNumber = $lastCaseNumber + 1;
                $newCaseId = "CA{$newCaseNumber}";
            } 
            else {
                $newCaseId = "CA{$timestamp}01";
            }

            $caseData = CourtCase::create([
                'caseId' => $newCaseId,
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_category' => $request->case_category,
                'case_section' =>$request->case_section,
                'case_stage' => $request->case_stage,
                'court_id' => $request->court_id,
                'court_branch' => $request->court_branch,
                'fees' => $request->fees,
                'branch' => $request->branch,
                'priority' => $request->priority,
                'comments' => $request->comments,
                'opposition_phone' => $request->opposition_phone,
                'opposition_name' => ucfirst($request->opposition_name),
                'witnesses' => $witnesses,
                'created_by'=>Auth::user()->id
            ]);
    
            $createdDocuments = [];
            
            if ($request->has('case_doc_name') && is_array($request->case_doc_name)) {
                foreach ($request->case_doc_name as $index => $name) {
                    $data = [
                        'courtCase_id' => $caseData->id,
                        'name' => $name,
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
    
    public function update(CourtCaseRequest $request,$caseId){
        
        DB::beginTransaction();
        try{
            $caseData=CourtCase::where('caseId',$caseId)->first();
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
                'caseId' => $caseData->caseId,
                'clientId' => $request->clientId,
                'client_type' => $request->client_type,
                'case_type' => $request->case_type,
                'case_category' => $request->case_category,
                'case_section' => $request->case_section,
                'case_stage' => $request->case_stage,
                'witnesses' => $witnesses?? $caseData->witnesses,
                'court_id' => $request->court_id,
                'fees' => $request->fees,
                'comments' => $request->comments,
                'branch' => $request->branch,
                'priority' => $request->priority,
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

    public function delete($caseId){
        DB::beginTransaction();
        try{
            $caseData=CourtCase::where('caseId',$caseId)->first();
            if(! $caseData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
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

    public function show($caseId){
        try {
            $case = CourtCase::where('caseId',$caseId)->first();
            $caseData =new CourtCaseResource($case);
            $caseHistory=CaseHistory::where('caseId',$case->caseId)->orderBy('id', 'DESC')->get();
            $caseFee = CaseFee::where('caseId',$case->caseId)->orderBy('id', 'DESC')->get();
            $caseExtraFee = CaseExtraFee::where('caseId',$case->caseId)->orderBy('id', 'DESC')->get();
            $expense = Expense::where('caseId',$case->caseId)->orderBy('id', 'DESC')->get();
            
            return response()->json([
                 'case' =>$caseData,
                 'caseHistory' => CaseHistoryResource::collection($caseHistory),
                 'caseFee' => CaseFeeResource::collection($caseFee),
                 'caseExtraFee' => CaseExtraFeeResource::collection($caseExtraFee),
                 'expense' => ExpenseResource::collection($expense),
                 'status'=>200
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    }

    public function case_lawer_store(Request $request,$caseId){
        
        $request->validate([
            'case_lawer_id' => 'required|exists:users,id',
        ]);
        try{
            DB::beginTransaction();
            $case=CourtCase::where('caseId',$caseId)->first();
            if(!$case){
                return response([
                    'message' => 'Case Id Not found'
                ]); 
            }
            $case->case_lawer_id=$request->case_lawer_id;
            $case->save();
            DB::commit();
            return response([
                'message' => ' Added Successfully'
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
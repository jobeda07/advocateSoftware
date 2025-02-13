<?php

namespace App\Http\Controllers\Api;

use App\Models\CaseHistory;
use App\Traits\ImageUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\CaseHistoryRequest;
use App\Http\Resources\CaseHistoryResource;

class CaseHistoryAction extends Controller
{ 
    use ImageUpload; 
    public function index(Request $request){
        try {
             $search=$request->query('search');
             $query=CaseHistory::orderBy('id', 'DESC');
             if($search){
                $query->where(function ($q) use ($search){
                    $q->Where("hearing_date_time","like","%{$search}%");
                });
             }
            $caseHistory = $query->paginate(100)->appends($request->query());
            if ($caseHistory->isEmpty()) {
                return response()->json(['data' => []], 404);
            }
            return CaseHistoryResource::collection($caseHistory)
                ->additional(['status' => 200]);
         
        } catch (\Exception $e) {
            return response()->json([
                'error' =>'data not found',
                 'status'=>500
            ]);
        }
    } 
    public function store(CaseHistoryRequest $request)
    {   

        DB::beginTransaction();
        try {         
            $caseHistoryData = CaseHistory::create([
                'caseId' => $request->caseId,
                'hearing_date_time' => $request->hearing_date_time,
                'activity' => $request->activity,
                'court_decition' => $request->court_decition,
                'remarks' => $request->remarks,
                'created_by' =>  Auth::user()->id,
            ]);
            if ($request->hasFile('case_history_image')) {
                $file = $request->case_history_image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/caseHistoryImage/', true);
                $caseHistoryData->case_history_image = 'uploads/images/caseHistoryImage/' . $filename;
            }

            if ($request->hasFile('case_history_pdf')) {
                $file = $request->case_history_pdf;
                $pdfName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pdf/caseHistoryPDF/'), $pdfName);
                $caseHistoryData->case_history_pdf= 'uploads/pdf/caseHistoryPDF/' . $pdfName;
            }
            $caseHistoryData->save();
    
            DB::commit();
            
            return response([
                'case-data' => new CaseHistoryResource($caseHistoryData),
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
    
    public function update(CaseHistoryRequest $request,$id){

        DB::beginTransaction();
        try{
            $caseHistoryData=CaseHistory::find($id);
            //dd($caseHistoryData);
            if(! $caseHistoryData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            $caseHistoryData->update([
                'caseId' => $request->caseId,
                'hearing_date_time' => $request->hearing_date_time,
                'activity' => $request->activity,
                'court_decition' => $request->court_decition,
                'remarks' => $request->remarks,
                'created_by' => $caseHistoryData->created_by,
            ]);
            if ($request->hasFile('case_history_image')) {
                $this->deleteOne($caseHistoryData->case_history_image);
                $file = $request->case_history_image;
                $filename = $this->imageUpload($file, 1000, 1000, 'uploads/images/caseHistoryImage/', true);
                $caseHistoryData->case_history_image = 'uploads/images/caseHistoryImage/' . $filename;
            }

        
            if ($request->hasFile('case_history_pdf')) {
                $removefile = public_path($caseHistoryData->case_history_pdf);
                File::delete($removefile);
                $file = $request->case_history_pdf;
                $pdfName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/pdf/caseHistoryPDF/'), $pdfName);
                $caseHistoryData->case_history_pdf= 'uploads/pdf/caseHistoryPDF/' . $pdfName;
            }

            DB::commit();
            return response([
                'case-data'=> new CaseHistoryResource($caseHistoryData),
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
            $caseHistoryData=CaseHistory::find($id);
            if(!$caseHistoryData){
                return response()->json([
                    'error' =>'data not found',
                     'status'=>500
                ]);
            }
            if ($caseHistoryData->case_history_image) {
                $this->deleteOne($caseHistoryData->case_history_image);
            }
            if ($caseHistoryData->case_history_pdf) {
                $removefile = public_path($caseHistoryData->case_history_pdf);
                File::delete($removefile);
            }
            $caseHistoryData->delete();
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

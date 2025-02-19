<?php

namespace App\Http\Resources;

use App\Models\CaseExtraFee;
use App\Models\CaseFee;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CaseSection;
use App\Models\Hearing;
use App\Models\User;
use DateTime;

class CourtCaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
//    public function toArray(Request $request): array
//    {
//        $caseSec = explode(',', $this->case_section);
//        $caselawers = explode(',', $this->case_lawer_id );
//        $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
//        $lawer = User::whereIn('id', $caselawers)->get();
//        $hearing=Hearing::where('caseId',$this->caseId)->latest()->first();
//        return [
//           // 'id' => $this->id,
//            'caseId' => $this->caseId,
//            'clientId' => $this->clientId ?? 'N/A',
//            'client_name' => $this->clientAdd->name ?? 'N/A',
//            'client_phone' => $this->clientAdd->phone ?? 'N/A',
//            'fathers_name' => $this->clientAdd->fathers_name ?? 'N/A',
//            'profession' => $this->clientAdd->profession ?? 'N/A',
//            'case_section' => $caseSections->toArray(),
//            'case_type' => $this->caseType->name ?? 'N/A',
//            'case_category' => $this->caseCategory->name ?? 'N/A',
//            'case_stage' => $this->caseStage->name ?? 'N/A',
//            'client_type' => $this->clientType->name ?? 'N/A',
//            'fees' => $this->fees ?? 'N/A',
//            'priority' => $this->priority ?? 'N/A',
//            'court_branch' => $this->court_branch ?? 'N/A',
//            'court' => $this->courtAdd->name ?? 'N/A',
//            'opposition_name' => $this->opposition_name ?? 'N/A',
//            'opposition_phone' => $this->opposition_phone ?? 'N/A',
//            'branch' => $this->branch ?? 'N/A',
//            'priority' => $this->priority ?? 'N/A',
//            'comments' => $this->comments ?? 'N/A',
//            'witnesses' =>$this->witnesses ?? 'N/A',
//            'created_by' =>$this->createdBy->name ?? 'N/A',
//            'case_lawer' => $lawer->map(function ($user){
//                return[
//                   'id'=>$user->id,
//                   'name'=>$user->name,
//                   'email'=>$user->email,
//                   'phone'=>$user->phone,
//                ];
//            }),
//            'next_hearing' => isset($hearing->date_time) ? (new DateTime($hearing->date_time))->format('j F Y g.i A') : '',
//            'case_documents' => $this->caseDocument->map(function ($doc) {
//                return [
//                    'id' => $doc->id,
//                    'name' => $doc->name,
//                    'case_image' =>$doc->case_image ?? '',
//                    'case_pdf' => $doc->case_pdf ?? '',
//                ];
//            }),
//            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
//        ];
//    }


    public function toArray(Request $request): array
    {
        $caseSec = explode(',', $this->case_section);
        $caselawers = explode(',', $this->case_lawer_id);
        $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
        $lawer = User::whereIn('id', $caselawers)->get();
        $hearing = Hearing::where('caseId', $this->caseId)->latest()->first();

        // Calculate total case expenses
        $case_expense = Expense::where('caseId', $this->caseId)->sum('amount');

        // Calculate total paid amount from CaseFee and CaseExtraFee
        $total_paid = CaseFee::where('caseId', $this->caseId)->sum('amount') +
            CaseExtraFee::where('caseId', $this->caseId)->sum('amount');

        // Calculate due amount
        $due = $this->fees - CaseFee::where('caseId', $this->caseId)->sum('amount');

        // Calculate current profit
        $current_profit = $total_paid - $case_expense;

        return [
            'caseId' => $this->caseId,
            'clientId' => $this->clientId ?? 'N/A',
            'client_name' => $this->clientAdd->name ?? 'N/A',
            'client_phone' => $this->clientAdd->phone ?? 'N/A',
            'fathers_name' => $this->clientAdd->fathers_name ?? 'N/A',
            'profession' => $this->clientAdd->profession ?? 'N/A',
            'case_section' => $caseSections->toArray(),
            'case_type' => $this->caseType->name ?? 'N/A',
            'case_category' => $this->caseCategory->name ?? 'N/A',
            'case_stage' => $this->caseStage->name ?? 'N/A',
            'client_type' => $this->clientType->name ?? 'N/A',
            'fees' => $this->fees ?? 'N/A',
            'priority' => $this->priority ?? 'N/A',
            'court_branch' => $this->court_branch ?? 'N/A',
            'court' => $this->courtAdd->name ?? 'N/A',
            'opposition_name' => $this->opposition_name ?? 'N/A',
            'opposition_phone' => $this->opposition_phone ?? 'N/A',
            'branch' => $this->branch ?? 'N/A',
            'priority' => $this->priority ?? 'N/A',
            'comments' => $this->comments ?? 'N/A',
            'witnesses' => $this->witnesses ?? 'N/A',
            'created_by' => $this->createdBy->name ?? 'N/A',
            'case_lawer' => $lawer->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
            }),
            'next_hearing' => isset($hearing->date_time) ? (new DateTime($hearing->date_time))->format('j F Y g.i A') : '',
            'case_documents' => $this->caseDocument->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'case_image' => $doc->case_image ?? '',
                    'case_pdf' => $doc->case_pdf ?? '',
                ];
            }),
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),

            // Additional values
            'total_fees' => $this->fees ?? 0,
            'total_paid' => $total_paid,
            'due' => $due,
            'case_expense' => $case_expense,
            'current_profit' => $current_profit,
        ];
    }
}

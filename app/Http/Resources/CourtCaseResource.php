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

    public function toArray(Request $request): array
    {
        $caseSec = explode(',', $this->case_section);
        $caselawers = explode(',', $this->case_lawer_id);
        $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
        $lawer = User::whereIn('id', $caselawers)->get();
        $hearing = Hearing::where('caseId', $this->caseId)->latest()->first();

        $case_expense = Expense::where('caseId', $this->caseId)->sum('amount') ?? 0;
        $total_paid = CaseFee::where('caseId', $this->caseId)->sum('amount') ?? 0;
        $total_extra_paid = CaseExtraFee::where('caseId', $this->caseId)->sum('amount') ?? 0;
        $due = $this->fees - $total_paid;
        $current_profit = ($total_paid + $total_extra_paid) - ($case_expense + $due);

        return [
            'caseId' => $this->caseId,
            'clientId' => $this->clientId ?? '',
            'client_name' => $this->clientAdd->name ?? '',
            'client_phone' => $this->clientAdd->phone ?? '',
            'fathers_name' => $this->clientAdd->fathers_name ?? '',
            'profession' => $this->clientAdd->profession ?? '',
            'case_section' => $this->case_section,
            'case_type' => $this->caseType->name ?? '',
            'case_category' => $this->caseCategory->name ?? '',
            'case_stage' => $this->caseStage->name ?? '',
            'client_type' => $this->clientType->name ?? '',
            'fees' => $this->fees ?? '',
            'priority' => $this->priority ?? '',
            'court_branch' => $this->court_branch ?? '',
            'court' => $this->courtAdd->name ?? '',
            'opposition_name' => $this->opposition_name ?? '',
            'opposition_phone' => $this->opposition_phone ?? '',
            'branch' => $this->branch ?? '',
            'priority' => $this->priority ?? '',
            'comments' => $this->comments ?? '',
            'witnesses' => $this->witnesses ?? '',
            'created_by' => $this->createdBy->name ?? '',
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

            'total_fees' => $this->fees ?? 0,
            'total_paid' => $total_paid,
            'total_extra_paid' => $total_extra_paid,
            'due' => $due,
            'case_expense' => $case_expense,
            'current_profit' => $current_profit,
        ];
    }
}

<?php

namespace App\Http\Resources;

use DateTime;
use App\Models\User;
use App\Models\Hearing;
use App\Models\CaseSection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndexCourtCaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $caseSec = explode(',', $this->case_section);
        $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');

        $hearing=Hearing::where('caseId',$this->caseId)->latest()->first();
        $caselawers = explode(',', $this->case_lawer_id );
        $lawer = User::whereIn('id', $caselawers)->get();
        return [
            'caseId' => $this->caseId,
            'clientId' => $this->clientId ?? '',
            'client_name' => $this->clientAdd->name ?? '',
            'client_phone' => $this->clientAdd->phone ?? '',
            'case_section' => $this->case_section,
            'case_category' => $this->caseCategory->name ?? '',
            'priority' => $this->priority,
            'case_type' => $this->caseType->name ?? '',
            'case_stage' => $this->caseStage->name ?? '',
            'client_type' => $this->clientType->name ?? '',
            'court' => $this->courtAdd->name ?? '',
            'next_hearing' => isset($hearing->date_time) ? (new DateTime($hearing->date_time))->format('j F Y g.i A') : '',
            'case_lawer' => $this->caselawer->name ?? '',
            'case_lawer' => $lawer->pluck('name')->implode(', '),
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
        ];
    }
}

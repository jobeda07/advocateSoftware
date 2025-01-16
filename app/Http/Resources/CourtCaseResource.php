<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\CaseSection;

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
        $caseSections = CaseSection::whereIn('id', $caseSec)->pluck('section_code');
        return [
            'id' => $this->id,
            'caseID' => $this->caseID,
            'clientId' => $this->client->clientId ?? 'N/A',
            'client_name' => $this->client->name ?? 'N/A',
            'client_phone' => $this->client->phone ?? 'N/A',
            'fathers_name' => $this->client->fathers_name ?? 'N/A',
            'case_section' => $caseSections->toArray(),
            'case_type' => $this->caseType->name ?? 'N/A',
            'case_category' => $this->caseCategory->name ?? 'N/A',
            'case_stage' => $this->caseStage->name ?? 'N/A',
            'client_type' => $this->clientType->title ?? 'N/A',
            'fees' => $this->fees ?? 'N/A',
            'court_branch' => $this->court_branch ?? 'N/A',
            'court' => $this->courtAdd->name ?? 'N/A',
            'opposition_name' => $this->opposition_name ?? 'N/A',
            'opposition_phone' => $this->opposition_phone ?? 'N/A',
            'branch' => $this->branch ?? 'N/A',
            'comments' => $this->comments ?? 'N/A',
            'witnesses' =>$this->witnesses ?? 'N/A',
            'case_documents' => $this->caseDocument->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'name' => $doc->name,
                    'case_image' =>$doc->case_image ? env('APP_URL') . "/" .$doc->case_image : '',
                    'case_pdf' => $doc->case_pdf ? env('APP_URL') . "/" .$doc->case_pdf : '',
                ];
            }),
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
        ];
    }
}

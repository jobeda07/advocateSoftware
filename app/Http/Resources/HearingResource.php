<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HearingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'caseId' => $this->caseId ?? '',
            'case_category' => $this->caseOf->caseCategory->name?? '',
            'case_stage' => $this->caseOf->caseStage->name?? '',
            'client_Type' => $this->caseOf->clientType->name ?? '',
            'client_name' => $this->caseOf->clientAdd->name ?? '',
            'client_phone' => $this->caseOf->clientAdd->phone ?? '',
            'court_name' => $this->courtOf->name ?? '',
            'date_time' => $this->date_time ?? '',
            'court_branch' => $this->court_branch ?? '',
            'comment' => $this->comment ?? '',
            'createdBy' => $this->createdBy->name,
            'inform' => 'No',
            'create_date_time' => $this->created_at->format('j F Y  g.i A')
        ];
    }
}

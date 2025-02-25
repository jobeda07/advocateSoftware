<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseHistoryResource extends JsonResource
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
            'hearing_date_time' => $this->hearing_date_time ?? '',
            'activity' => $this->activity ?? '',
            'court_decition' => $this->court_decition ?? '',
            'remarks' => $this->remarks ?? '',
            'createdBy' => $this->createdBy->name ?? '',
            'case_history_image' =>$this->case_history_image ?? '',
            'case_history_pdf' => $this->case_history_pdf ?? '',
            'create_date_time' => $this->created_at->format('j F Y  g.i A')
        ];
    }
}

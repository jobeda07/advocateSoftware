<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseTaskResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $caselawers = explode(',', $this->assign_to );
        $lawer = User::whereIn('id', $caselawers)->get();
        return [
            'id'=>$this->id,
            'caseId'=>$this->caseId,
            'title'=>$this->title,
            'details'=>$this->details,
            'priority'=>$this->priority,
            'date'=>$this->date,
            'assign_to' => $lawer->pluck('name')->implode(', '),
            'created_by' => $this->createdBy->name ?? '',
        ];
    }
}

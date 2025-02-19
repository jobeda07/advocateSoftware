<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return[
            'id' => $this->id,
            'visitorId' => $this->visitorId,
            'name' => $this->name ?? '',
            'phone' => $this->phone ?? '',
            'case_type' => $this->casetype->name ?? '',
            'priority' => $this->priority,
            'remark'=>$this->remark ?? '',
            'created_by' => $this->createdBy->name ?? '',
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
        ];
    }
}

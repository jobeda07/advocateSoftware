<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseExtraFeeResource extends JsonResource
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
            'transaction_no' => $this->transaction_no,
            'caseId' => $this->caseId ?? '',
            'client_name' => $this->caseOf->clientAdd->name ?? '',
            'client_phone' => $this->caseOf->clientAdd->phone ?? '',
            'amount' => $this->amount ?? '',
            'payment_type' => $this->payment_type ?? '',
            'comment' => $this->comment ?? '',
            'createdBy' => $this->createdBy->name,
            'create_date_time' => $this->created_at->format('j F Y  g.i A')
        ];
    }
}

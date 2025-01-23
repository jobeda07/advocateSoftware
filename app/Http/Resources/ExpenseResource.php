<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'caseId' => $this->caseId,
            'expense_category' => $this->expense_category->name ?? '',
            'amount' => $this->amount,
            'payment_type' => $this->payment_type,
            'comment' => $this->comment,
            'created_by' => $this->createdBy->name ?? '',
            'create_date_time' => $this->created_at->format('j F Y  g.i A')
        ];
    }
}

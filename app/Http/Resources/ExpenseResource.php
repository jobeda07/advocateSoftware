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
            'expenseId' => $this->transaction_no,
            'caseId' => $this->caseId,
            'expense_category' => $this->expense_category->name ?? '',
            'amount' => $this->amount,
            'payment_method' => $this->payment_method,
            'comment' => $this->comment,
            'created_by' => $this->createdBy->name ?? '',
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
            'voucher_image' => $this->voucher_image ?? ''
        ];
    }
}

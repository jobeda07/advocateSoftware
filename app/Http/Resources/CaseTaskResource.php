<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TaskProgress;
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
        $caselawers = explode(',', $this->assign_to);
        $lawer = User::whereIn('id', $caselawers)->get();
        $TaskProgress = TaskProgress::where('case_task_id', $this->id)->sum('progress');
        return [
            'id' => $this->id,
            'caseId' => $this->caseId,
            'title' => $this->title,
            'details' => $this->details,
            'priority' => $this->priority,
            'date' => $this->date,
            'taskProgress' => (int) $TaskProgress,
            'assign_to' => $lawer->map(fn($lawyer) => [
                'id' => $lawyer->id,
                'name' => $lawyer->name
            ]),
            'created_by' => $this->createdBy->name ?? '',
            'create_date_time' => $this->created_at->format('j F Y g.i A')
        ];
    }
}

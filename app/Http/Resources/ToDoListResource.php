<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ToDoListResource extends JsonResource
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
            'title' => $this->title,
            'deadline' => Carbon::parse($this->deadline)->format('d-m-Y'),
            'note' => $this->note,
            'create_date_time' => $this->created_at->format('j F Y  g.i A')
        ];
    }
}

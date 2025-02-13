<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email ?? '',
            'join_date' => $this->join_date,
            'status' => $this->status == 1 ? 'active' : 'inactive',
            'portfolio_status' => $this->portfolio_status == 1 ? 'active' : 'inactive',
            'designation' => $this->designation,
            'expertise_in' => $this->expertise_in,
            'address' => $this->address,
            'image' =>$this->image ? $this->image : '',
            'role_id' => method_exists($this->resource, 'getRoleIds') ? $this->resource->getRoleIds()->join(', ') : '',
            'role_name' => method_exists($this->resource, 'getRoleNames') ? $this->resource->getRoleNames()->join(', ') : '',
        ];
    }
}

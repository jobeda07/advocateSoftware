<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected $extraData;

    public function __construct($resource, $extraData = [])
    {
        parent::__construct($resource);
        $this->extraData = $extraData;
    }

    public function toArray(Request $request): array
    {
        return array_merge([
            'clientId' => $this->clientId ?? '',
            'name' => $this->name ?? '',
            'phone' => $this->phone ?? '',
            'email' => $this->email ?? '',
            'fathers_name' => $this->fathers_name ?? '',
            'alternative_phone' => $this->alternative_phone ?? '',
            'profession' => $this->profession ?? '',
            'division_id' => $this->division_id ?? '',
            'district_id' => $this->district_id ?? '',
            'thana_id' => $this->thana_id ?? '',
            'address' => $this->address ?? '',
            'reference' => $this->reference ?? '',
            'created_by' => $this->createdBy->name ?? '',
            'create_date_time' => $this->created_at->format('j F Y  g.i A'),
        ], $this->extraData);
    }
}

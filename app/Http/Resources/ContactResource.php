<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
                'image' => $this->image,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'address' => $this->address,
                'email' => $this->email,
                'phone' => $this->phone,
                'facebook_link' => $this->facebook_link,
                'location_details' => $this->location_details,
             ];
    }
}

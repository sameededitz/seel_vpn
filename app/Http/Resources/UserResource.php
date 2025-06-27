<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'slug' => $this->slug,
            'email' => $this->email,
            'email_verified' => !is_null($this->email_verified_at),
            'role' => $this->role,
            'created_at' => $this->created_at->toIso8601String(),
            'billing_address' => $this->whenLoaded('billingAddress', new BillingAddressResource($this->billingAddress))
        ];

    }
}

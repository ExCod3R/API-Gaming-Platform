<?php

namespace App\Http\Resources;

use App\Http\Resources\PackagePlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlayerResource extends JsonResource
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
            'role' => $this->getRoleNames()->first(),
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'country' => $this->country,
            'avatar' => $this->avatar ? asset('storage/' . $this->avatar) : null,
            'package_plan' => PackagePlanResource::make($this['packagePlan']),
        ];
    }
}

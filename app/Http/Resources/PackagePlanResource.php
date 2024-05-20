<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagePlanResource extends JsonResource
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
            'price' => $this->price,
            'package' => PackageResource::make($this->whenLoaded('package')),
            'package_term' => PackageTermResource::make($this->whenLoaded('packageTerm')),
        ];
    }
}

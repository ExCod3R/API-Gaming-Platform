<?php

namespace App\Http\Resources;

use App\Http\Resources\PackagePlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'description' => $this->description,
            'status' => $this->status,
            'package_plans' => PackagePlanResource::collection($this->whenLoaded('packagePlans')),
            'games' => GameTileResource::collection($this->whenLoaded('games')),
        ];
    }
}

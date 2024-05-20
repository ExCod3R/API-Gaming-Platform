<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'likes' => $this['rating']->likes ?? 0,
            'dislikes' => $this['rating']->dislikes ?? 0,
            'vote' => $this['vote'],
        ];
    }
}

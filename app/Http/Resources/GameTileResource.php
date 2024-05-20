<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GameTileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'thumbnail' => $this->thumbnail ? asset('storage/' . $this->thumbnail) : null,
            'animation' => $this->animation ? asset('storage/' . $this->animation) : null,
            'tile_size' => match ($this->pivot->tile_size) {
                1 => 'mediumTile',
                2 => 'largeTile',
                default => 'standardTile'
            },
            'is_hot' => $this->pivot->is_hot,
            'is_highlight' => $this->pivot->is_highlight,
            'is_paid' => $this->pivot->is_paid,
        ];
    }
}

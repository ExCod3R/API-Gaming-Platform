<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GamePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'game_id' => $this['selectGame']->id,
            'name' => $this['selectGame']->name,
            'description' => $this['selectGame']->description,
            'how_to_play' => $this['selectGame']->how_to_play,
            'thumbnail' => $this['selectGame']->thumbnail ? asset('storage/' . $this['selectGame']->thumbnail) : null,
            'orientation' => $this['selectGame']->orientation,
            'rating' => $this['rating'],
            'total_vote' => $this['totalVote'],
            'other_games' => GameTileResource::collection($this['otherGames']),
        ];
    }
}

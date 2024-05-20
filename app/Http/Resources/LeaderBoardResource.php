<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaderBoardResource extends JsonResource
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
            'player_id' => $this->player->id,
            'avatar' => $this->player->avatar ? asset('storage/' . $this->player->avatar) : null,
            'name' => $this->player->name,
            'country' => $this->player->country,
            'score' => $this->score,
        ];
    }
}

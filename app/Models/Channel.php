<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Channel extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function games()
    {
        return $this->belongsToMany(Game::class)
            ->withPivot(['tile_size', 'is_hot', 'is_highlight', 'is_paid', 'order_column'])
            ->withTimestamps();
    }
}

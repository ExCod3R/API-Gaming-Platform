<?php

namespace App\Models;

use App\Enums\TileSizeEnum;
use App\Models\Traits\Multitenantable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class ChannelGame extends Model implements Sortable
{
    use HasFactory;
    use Multitenantable;
    use SortableTrait;

    protected $table = 'channel_game';

    protected $guarded = [];

    protected $casts = [
        'tile_size' =>  TileSizeEnum::class,
    ];

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class);
    }
}

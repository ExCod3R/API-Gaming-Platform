<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum AdCategoryEnum: string implements HasLabel
{
    case HOME = 'home';
    case GAME_LEFT = 'game_left';
    case GAME_RIGHT = 'game_right';
    case GAME_BOTTOM = 'game_bottom';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::HOME => 'Home Page Ad',
            self::GAME_LEFT => 'Game Page Left Ad',
            self::GAME_RIGHT => 'Game Page Right Ad',
            self::GAME_BOTTOM => 'Game Page Bottom Ad',
        };
    }
}

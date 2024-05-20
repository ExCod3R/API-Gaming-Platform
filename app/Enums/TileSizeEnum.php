<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum TileSizeEnum: string implements HasLabel, HasColor
{
    case STANDARD = '0';
    case MEDIUM = '1';
    case LARGE = '2';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::STANDARD => 'Standard Tile',
            self::MEDIUM => 'Medium Tile',
            self::LARGE => 'Large Tile',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::STANDARD => 'info',
            self::MEDIUM => 'warning',
            self::LARGE => 'danger',
        };
    }
}

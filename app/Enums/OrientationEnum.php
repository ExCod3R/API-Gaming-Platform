<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum OrientationEnum: string implements HasLabel, HasColor, HasIcon
{
    case PORTRAIT = '0';
    case LANDSCAPE = '1';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::PORTRAIT => 'Portrait',
            self::LANDSCAPE => 'Landscape',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::PORTRAIT => 'info',
            self::LANDSCAPE => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::PORTRAIT => 'heroicon-m-arrows-up-down',
            self::LANDSCAPE => 'heroicon-m-arrows-right-left',
        };
    }
}

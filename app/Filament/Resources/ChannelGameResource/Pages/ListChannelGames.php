<?php

namespace App\Filament\Resources\ChannelGameResource\Pages;

use App\Filament\Resources\ChannelGameResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChannelGames extends ListRecords
{
    protected static string $resource = ChannelGameResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

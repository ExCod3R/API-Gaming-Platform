<?php

namespace App\Filament\Resources\ChannelGameResource\Pages;

use App\Filament\Resources\ChannelGameResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateChannelGame extends CreateRecord
{
    protected static string $resource = ChannelGameResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

namespace App\Filament\Resources\ChannelGameResource\Pages;

use App\Filament\Resources\ChannelGameResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChannelGame extends EditRecord
{
    protected static string $resource = ChannelGameResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

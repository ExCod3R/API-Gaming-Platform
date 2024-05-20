<?php

namespace App\Filament\Resources;

use App\Enums\TileSizeEnum;
use App\Filament\Resources\ChannelGameResource\Pages;
use App\Models\ChannelGame;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChannelGameResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'update',
        ];
    }

    protected static ?string $model = ChannelGame::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Channel Game Management';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('tile_size')
                                    ->options(TileSizeEnum::class)
                                    ->required(),

                                Forms\Components\Toggle::make('is_hot'),

                                Forms\Components\Toggle::make('is_highlight'),

                                Forms\Components\Toggle::make('is_paid'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('game.thumbnail')
                    ->label('Thumbnail')
                    ->circular(),

                Tables\Columns\TextColumn::make('game.name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('tile_size')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_hot')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_highlight')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_paid')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('order_column', 'asc')
            ->paginated([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('tile_size')
                    ->options(TileSizeEnum::class),

                Tables\Filters\TernaryFilter::make('is_hot')
                    ->boolean()
                    ->trueLabel('Only Hot Games')
                    ->falseLabel('Only Not Hot Games'),

                Tables\Filters\TernaryFilter::make('is_highlight')
                    ->boolean()
                    ->trueLabel('Only Highlight Games')
                    ->falseLabel('Only Not Highlight Games'),

                Tables\Filters\TernaryFilter::make('is_paid')
                    ->boolean()
                    ->trueLabel('Only Paid Games')
                    ->falseLabel('Only Free Games'),
            ])
            ->actions([
                Tables\Actions\Action::make('up')
                    ->action(fn (ChannelGame $record) => $record->moveOrderUp())
                    ->icon('heroicon-m-arrow-up')->iconButton(),
                Tables\Actions\Action::make('down')
                    ->action(fn (ChannelGame $record) => $record->moveOrderDown())
                    ->icon('heroicon-m-arrow-down')->iconButton(),
                Tables\Actions\EditAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChannelGames::route('/'),
            'create' => Pages\CreateChannelGame::route('/create'),
            'edit' => Pages\EditChannelGame::route('/{record}/edit'),
        ];
    }
}

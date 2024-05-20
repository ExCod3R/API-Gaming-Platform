<?php

namespace App\Filament\Resources\ChannelResource\RelationManagers;

use App\Enums\TileSizeEnum;
use App\Models\ChannelGame;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;

class GamesRelationManager extends RelationManager
{
    protected static string $relationship = 'games';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),

                Tables\Columns\TextColumn::make('tile_size')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_hot')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_highlight')
                    ->boolean()
                    ->sortable(),
            ])
            ->defaultSort('order_column', 'asc')
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select->placeholder('Select a game')
                    )
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('tile_size')
                            ->options(TileSizeEnum::class)
                            ->required(),

                        Forms\Components\Toggle::make('is_hot'),

                        Forms\Components\Toggle::make('is_highlight'),

                        Forms\Components\Hidden::make('order_column')
                            ->default(function () {
                                $order_column = ChannelGame::max('order_column');
                                return $order_column ? $order_column + 1 : 1;
                            }),
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }
}

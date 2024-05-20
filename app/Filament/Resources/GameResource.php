<?php

namespace App\Filament\Resources;

use App\Enums\OrientationEnum;
use App\Filament\Resources\GameResource\Pages;
use App\Models\Game;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class GameResource extends Resource implements HasShieldPermissions
{
    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    protected static ?string $model = Game::class;

    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Game Management';

    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Game Name')
                                    ->required()
                                    ->live(debounce: 500)
                                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                        if (($get('slug') ?? '') !== Str::slug($old)) {
                                            return;
                                        }

                                        $set('slug', Str::slug($state));
                                    }),

                                Forms\Components\TextInput::make('slug')
                                    ->required()
                                    ->unique(Game::class, 'slug', fn ($record) => $record),

                                Forms\Components\Textarea::make('description')
                                    ->required()
                                    ->rows(2)
                                    ->minLength(50)
                                    ->maxLength(65535)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),

                                Forms\Components\Textarea::make('how_to_play')
                                    ->required()
                                    ->rows(2)
                                    ->minLength(50)
                                    ->maxLength(65535)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),
                            ])->columns(2),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Radio::make('orientation')
                                    ->options(OrientationEnum::class)
                                    ->inline()
                                    ->inlineLabel(false)
                                    ->required(),

                                Forms\Components\Toggle::make('status')
                                    ->helperText('Active or inactive game')
                                    ->default(true)
                            ])->columns(2),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('thumbnail')
                                    ->required()
                                    ->image()
                                    ->acceptedFileTypes(['image/webp'])
                                    ->maxSize(1024)
                                    ->directory('thumbnail'),

                                Forms\Components\FileUpload::make('animation')
                                    ->required()
                                    ->acceptedFileTypes(['video/webm'])
                                    ->directory('animation'),

                                Forms\Components\FileUpload::make('game_file')
                                    ->required()
                                    ->acceptedFileTypes(['application/zip', 'application/octet-stream', 'multipart/x-zip', 'application/zip-compressed', 'application/x-zip-compressed', 'application/x-zip'])
                                    ->directory('game_file'),
                            ]),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail'),

                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('orientation')
                    ->badge()
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->paginated([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('orientation')
                    ->options(OrientationEnum::class),

                Tables\Filters\TernaryFilter::make('status')
                    ->boolean()
                    ->trueLabel('Only Active')
                    ->falseLabel('Only Inactive'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->iconButton(),
                Tables\Actions\DeleteAction::make()->iconButton(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListGames::route('/'),
            'create' => Pages\CreateGame::route('/create'),
            'edit' => Pages\EditGame::route('/{record}/edit'),
        ];
    }
}

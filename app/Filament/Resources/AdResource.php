<?php

namespace App\Filament\Resources;

use App\Enums\AdCategoryEnum;
use App\Filament\Resources\AdResource\Pages;
use App\Models\Ad;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AdResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = Ad::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ad Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('ad_category')
                                    ->options(AdCategoryEnum::class)
                                    ->required(),

                                Forms\Components\TextInput::make('name')
                                    ->required(),

                                Forms\Components\TextInput::make('redirecting_link')
                                    ->required()
                                    ->url()
                                    ->columnSpanFull(),

                                Forms\Components\DatePicker::make('published_date')
                                    ->required()
                                    ->default(now()),

                                Forms\Components\DatePicker::make('expire_date')
                                    ->required(),

                                Forms\Components\TextInput::make('view_limit')
                                    ->numeric()
                                    ->required(),

                                Forms\Components\Select::make('countries')
                                    ->helperText('If you need this ad to reach all counties, leave this field empty.')
                                    ->relationship('counties', 'name')
                                    ->multiple()
                                    ->preload(),
                            ])->columns(2),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->required()
                                    ->image()
                                    ->acceptedFileTypes(['image/webp'])
                                    ->maxSize(1024)
                                    ->directory('ads'),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Is Active')
                                    ->helperText('Active or inactive branch')
                                    ->default(true),
                            ]),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('ad_category')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('published_date')
                    ->searchable(),

                Tables\Columns\TextColumn::make('expire_date')
                    ->searchable(),

                Tables\Columns\TextColumn::make('view_limit')
                    ->badge()
                    ->searchable(),

                Tables\Columns\TextColumn::make('total_views')
                    ->badge()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),

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
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit'),
        ];
    }
}

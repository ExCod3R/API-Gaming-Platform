<?php

namespace App\Filament\Resources;

use App\Enums\RoleEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class UserResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'User Management';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('email')
                                    ->email()
                                    ->required()
                                    ->unique(ignoreRecord: true),

                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->afterStateHydrated(function (Forms\Components\TextInput $component, $state) {
                                        $component->state('');
                                    })
                                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                                    ->dehydrated(fn ($state) => filled($state))
                                    ->required(fn (string $context): bool => $context === 'create'),

                                Forms\Components\Toggle::make('status')
                                    ->helperText('Active or inactive user')
                                    ->default(true)
                            ]),
                    ]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Super Admin Area')
                            ->schema([
                                Forms\Components\Select::make('roles')
                                    ->relationship('roles', 'name')
                                    ->preload()
                                    ->required(),

                                Forms\Components\Select::make('channel_id')
                                    ->relationship('channel', 'name')
                                    ->required(),
                            ]),
                    ])->hidden(!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('channel.name')
                    ->badge()
                    ->sortable()
                    ->hidden(!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)),

                Tables\Columns\TextColumn::make('roles.name')
                    ->formatStateUsing(fn ($state): string => Str::headline($state))
                    ->badge()
                    ->sortable()
                    ->hidden(!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)),

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
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('channel')
                    ->relationship('channel', 'name')
                    ->preload()
                    ->hidden(!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)),

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->preload()
                    ->hidden(!auth()->user()->hasRole(RoleEnum::SUPER_ADMIN->value)),

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
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

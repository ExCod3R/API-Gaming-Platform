<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BlogPostResource extends Resource implements HasShieldPermissions
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

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $label = 'Post';

    protected static ?string $navigationGroup = 'Blog Management';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('title')
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
                                    ->unique(BlogPost::class, 'slug', fn ($record) => $record),

                                Forms\Components\Textarea::make('excerpt')
                                    ->required()
                                    ->rows(2)
                                    ->minLength(50)
                                    ->maxLength(1000)
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),

                                Forms\Components\FileUpload::make('banner')
                                    ->required()
                                    ->image()
                                    ->maxSize(1024)
                                    ->directory('blog')
                                    ->columnSpan([
                                        'sm' => 2,
                                    ]),

                                Forms\Components\Toggle::make('is_url')
                                    ->label('Is URL')
                                    ->helperText('If this post content page is located elsewhere, please turn on this switch.')
                                    ->live()
                                    ->default(false),

                                Forms\Components\TextInput::make('url')
                                    ->required()
                                    ->url()
                                    ->columnSpan([
                                        'sm' => 2,
                                    ])
                                    ->hidden(fn (Get $get) => !$get('is_url')),

                                Forms\Components\RichEditor::make('content')
                                    ->required()
                                    ->toolbarButtons([
                                        'attachFiles',
                                        'blockquote',
                                        'bold',
                                        'bulletList',
                                        'codeBlock',
                                        'h2',
                                        'h3',
                                        'italic',
                                        'link',
                                        'orderedList',
                                        'redo',
                                        'strike',
                                        'undo',
                                    ])
                                    ->columnSpan([
                                        'sm' => 2,
                                    ])
                                    ->hidden(fn (Get $get) => $get('is_url')),
                            ]),
                    ])->columnSpan(2),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Select::make('blogCategories')
                                    ->label('Categories')
                                    ->relationship('blogCategories', 'name')
                                    ->multiple()
                                    ->preload()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),

                                Forms\Components\Toggle::make('is_active')
                                    ->label('Is Active')
                                    ->helperText('Active or inactive branch')
                                    ->default(true),
                            ]),

                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\Placeholder::make('created_at')
                                    ->label('Created at')
                                    ->content(fn (BlogPost $record): ?string => $record->created_at?->diffForHumans()),

                                Forms\Components\Placeholder::make('updated_at')
                                    ->label('Last modified at')
                                    ->content(fn (BlogPost $record): ?string => $record->updated_at?->diffForHumans()),
                            ])
                            ->hidden(fn (?BlogPost $record) => $record === null),
                    ]),
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('banner'),

                Tables\Columns\TextColumn::make('title')
                    ->limit(40)
                    ->searchable(),

                Tables\Columns\TextColumn::make('slug')
                    ->limit(25)
                    ->searchable(),

                Tables\Columns\TextColumn::make('blogCategories.name')
                    ->label('Categories')
                    ->badge(),

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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\BlogCategories;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\BlogCategories\Pages\ManageBlogCategories;
use App\Filament\Support\LocalizedField;
use App\Models\BlogCategory;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BlogCategoryResource extends Resource
{
    protected static ?string $model = BlogCategory::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.blog_categories');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.blog_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.blog_categories');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['name']),
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('admin_trans.general'))
                            ->schema([
                                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.name'))
                                    ->required()),
                                TextInput::make('order')
                                    ->label(__('admin_trans.order'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label(__('admin_trans.publish_status'))
                                    ->required(),
                            ]),
                        Tab::make(__('admin_trans.seo'))
                            ->schema([
                                LocalizedField::make('slug', fn (string $name) => TextInput::make($name)
                                    ->hiddenOn('create')
                                    ->required()
                                    ->label(__('admin_trans.slug'))),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('blogs_count')
                    ->label(__('admin_trans.blogs'))
                    ->counts('blogs'),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBlogCategories::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\ProjectCategories;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\ProjectCategories\Pages\ManageProjectCategories;
use App\Filament\Support\LocalizedField;
use App\Models\ProjectCategory;
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

class ProjectCategoryResource extends Resource
{
    protected static ?string $model = ProjectCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.project_categories');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.project_category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.project_categories');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TranslateContentAction::make(['name', 'short_text']),
            Tabs::make('tabs')->tabs([
                Tab::make(__('admin_trans.general'))->schema([
                    LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.name'))
                        ->required()
                        ->maxLength(255)),
                    LocalizedField::make('short_text', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.short_text'))
                        ->maxLength(255)),
                    TextInput::make('icon')
                        ->label(__('admin_trans.icon'))
                        ->placeholder('fas fa-futbol')
                        ->maxLength(100),
                    TextInput::make('order')
                        ->label(__('admin_trans.order'))
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Toggle::make('is_active')
                        ->label(__('admin_trans.publish_status'))
                        ->default(true),
                ]),
                Tab::make(__('admin_trans.seo'))->schema([
                    LocalizedField::make('slug', fn (string $name) => TextInput::make($name)
                        ->hiddenOn('create')
                        ->required()
                        ->label(__('admin_trans.slug'))),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('projects_count')
                    ->label(__('admin_trans.projects_nav'))
                    ->counts('projects'),
                TextColumn::make('icon')
                    ->label(__('admin_trans.icon'))
                    ->toggleable(),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
            ])
            ->defaultSort('order')
            ->reorderable('order')
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
            'index' => ManageProjectCategories::route('/'),
        ];
    }
}

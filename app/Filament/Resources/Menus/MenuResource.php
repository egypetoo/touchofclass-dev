<?php

namespace App\Filament\Resources\Menus;

use App\Enums\MenuRouteNameEnum;
use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Menus\Pages\ManageMenus;
use App\Filament\Support\LocalizedField;
use App\Models\Menu;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.menus_management');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.menu');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.menus_management');
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['title']),
                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.title'))
                    ->default(null)),
                Select::make('parent_id')
                    ->label(__('admin_trans.parent_menu'))
                    ->relationship('parent')
                    ->getOptionLabelFromRecordUsing(fn (Menu $record): string => (string) ($record->getTranslation('title', 'en')
                        ?: $record->getTranslation('title', 'ar')
                        ?: 'Menu #'.$record->id))
                    ->nullable(),
                Select::make('route_name')
                    ->label(__('admin_trans.route_name'))
                    ->options(MenuRouteNameEnum::class)
                    ->searchable()
                    ->required()
                    ->disabledOn('edit')
                    ->unique(),
                TextInput::make('order')
                    ->label(__('admin_trans.order'))
                    ->numeric()
                    ->default(null),
                Grid::make()
                    ->columns(3)
                    ->schema([
                        Toggle::make('is_active')
                            ->label(__('admin_trans.publish_status'))
                            ->required(),
                        Toggle::make('is_navbar')
                            ->label(__('admin_trans.is_navbar'))
                            ->required(),
                        Toggle::make('is_footer')
                            ->label(__('admin_trans.is_footer'))
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->formatStateUsing(function (array|string|null $state): string {
                        return (string) ($state ?? '');
                    })
                    ->searchable(),

                TextColumn::make('parent.title')
                    ->label(__('admin_trans.parent_menu'))
                    ->formatStateUsing(fn (array|string|null $state): string => (string) ($state ?? ''))
                    ->sortable(),

                TextColumn::make('order')
                    ->label(__('admin_trans.order'))
                    ->numeric()
                    ->sortable(),

                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                ToggleColumn::make('is_navbar')
                    ->label(__('admin_trans.is_navbar')),
                ToggleColumn::make('is_footer')
                    ->label(__('admin_trans.is_footer')),

                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('admin_trans.updated_at'))
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
            'index' => ManageMenus::route('/'),
        ];
    }
}

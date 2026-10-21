<?php

namespace App\Filament\Resources\Partners;

use App\Enums\ClientSectorEnum;
use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Partners\Pages\ManagePartners;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Partner;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class PartnerResource extends Resource
{
    protected static ?string $model = Partner::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.partners');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.partner');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.partners');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['name']),
                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.name'))
                    ->maxLength(255)),
                MultiImageUpload::make('images')
                    ->collection('partners')
                    ->label(__('admin_trans.images')),
                TextInput::make('url')
                    ->label(__('admin_trans.partner_url'))
                    ->url()
                    ->maxLength(2048),
                Select::make('sector')
                    ->label(__('admin_trans.client_sector'))
                    ->options(ClientSectorEnum::class)
                    ->nullable(),
                Toggle::make('show_in_home')
                    ->label(__('admin_trans.show_in_home'))
                    ->default(false),
                TextInput::make('order')
                    ->label(__('admin_trans.order'))
                    ->required()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->label(__('admin_trans.publish_status'))
                    ->default(true)
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('images')
                    ->label(__('admin_trans.image'))
                    ->collection('partners')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('url')
                    ->label(__('admin_trans.partner_url'))
                    ->searchable()
                    ->limit(50),
                TextColumn::make('sector')
                    ->label(__('admin_trans.client_sector'))
                    ->badge()
                    ->sortable(),
                ToggleColumn::make('show_in_home')
                    ->label(__('admin_trans.show_in_home')),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                TextColumn::make('order')
                    ->label(__('admin_trans.order'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            'index' => ManagePartners::route('/'),
        ];
    }
}

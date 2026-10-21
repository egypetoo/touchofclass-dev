<?php

namespace App\Filament\Resources\GalleryImages;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['title', 'description']),
                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.title'))
                    ->nullable()),
                LocalizedField::make('description', fn (string $name) => Textarea::make($name)
                    ->label(__('admin_trans.description'))
                    ->nullable()),
                MultiImageUpload::make('images')
                    ->label(__('admin_trans.image'))
                    ->collection('gallery_images'),
                TextInput::make('sort_order')
                    ->label(__('admin_trans.sort_order'))
                    ->default(0)
                    ->numeric()
                    ->nullable(),
                Toggle::make('is_active')
                    ->label(__('admin_trans.is_active'))
                    ->default(true)
                    ->nullable(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin_trans.title')),
                TextColumn::make('description')
                    ->label(__('admin_trans.description')),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.is_active')),
                TextColumn::make('sort_order')
                    ->label(__('admin_trans.sort_order')),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => ManageGalleryImages::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\BeforeAfters;

use App\Filament\Resources\BeforeAfters\Pages\ManageBeforeAfters;
use App\Models\BeforeAfter;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BeforeAfterResource extends Resource
{
    protected static ?string $model = BeforeAfter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.before_after');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.before_after');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.before_after');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            SpatieMediaLibraryFileUpload::make('before')
                ->label(__('admin_trans.before_image'))
                ->collection('before')
                ->required(),
            SpatieMediaLibraryFileUpload::make('after')
                ->label(__('admin_trans.after_image'))
                ->collection('after')
                ->required(),
            TextInput::make('order')
                ->label(__('admin_trans.order'))
                ->numeric()
                ->default(0),
            Toggle::make('is_active')
                ->label(__('admin_trans.publish_status'))
                ->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('before')
                    ->label(__('admin_trans.before_image'))
                    ->collection('before'),
                SpatieMediaLibraryImageColumn::make('after')
                    ->label(__('admin_trans.after_image'))
                    ->collection('after'),
                TextColumn::make('order')->label(__('admin_trans.order'))->sortable(),
                ToggleColumn::make('is_active')->label(__('admin_trans.publish_status')),
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
            'index' => ManageBeforeAfters::route('/'),
        ];
    }
}

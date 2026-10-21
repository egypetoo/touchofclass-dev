<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Teams\Pages\ManageTeams;
use App\Filament\Support\LocalizedField;
use App\Models\Team;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
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

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.teams');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.team');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.teams');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['name', 'title', 'bio']),
                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.name'))
                    ->required()
                    ->maxLength(255)),
                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.title'))
                    ->maxLength(255)),
                LocalizedField::make('bio', fn (string $name) => RichEditor::make($name)
                    ->label(__('admin_trans.description'))
                    ->default(null)),
                SpatieMediaLibraryFileUpload::make('image')
                    ->disk(config('media-library.disk_name', 'public'))
                    ->collection('teams')
                    ->label(__('admin_trans.image'))
                    ->acceptedFileTypes(['image/webp', 'image/jpeg', 'image/png'])
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120)
                    ->columnSpanFull(),
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
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('teams')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),
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
            'index' => ManageTeams::route('/'),
        ];
    }
}

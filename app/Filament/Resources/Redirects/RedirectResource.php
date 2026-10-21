<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\ManageRedirects;
use App\Models\Redirect;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowPath;

    protected static string|\UnitEnum|null $navigationGroup = null;

    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.redirects');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.redirect');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.redirects');
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'site';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('source')
                    ->label(__('admin_trans.redirect_source'))
                    ->required()
                    ->maxLength(2048)
                    ->helperText(__('admin_trans.redirect_source_help')),
                TextInput::make('target')
                    ->label(__('admin_trans.redirect_target'))
                    ->maxLength(2048)
                    ->helperText(__('admin_trans.redirect_target_help')),
                Select::make('status_code')
                    ->label(__('admin_trans.redirect_status_code'))
                    ->options([
                        301 => '301 — '.__('admin_trans.redirect_permanent'),
                        302 => '302 — '.__('admin_trans.redirect_temporary'),
                        410 => '410 — '.__('admin_trans.redirect_gone'),
                    ])
                    ->default(301)
                    ->required(),
                Toggle::make('is_active')
                    ->label(__('admin_trans.publish_status'))
                    ->default(true),
                Textarea::make('notes')
                    ->label(__('admin_trans.notes'))
                    ->rows(2)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('source')
                    ->label(__('admin_trans.redirect_source'))
                    ->searchable()
                    ->copyable(),
                TextColumn::make('target')
                    ->label(__('admin_trans.redirect_target'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('status_code')
                    ->label(__('admin_trans.redirect_status_code'))
                    ->badge(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                TextColumn::make('notes')
                    ->label(__('admin_trans.notes'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => ManageRedirects::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Faqs;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Filament\Support\LocalizedField;
use App\Models\Faq;
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
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use UnitEnum;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 10;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.faqs');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.faq');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.faqs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['question', 'answer']),
                LocalizedField::make('question', fn (string $name) => Textarea::make($name)
                    ->label(__('admin_trans.question'))
                    ->required()),
                LocalizedField::make('answer', fn (string $name) => Textarea::make($name)
                    ->label(__('admin_trans.answer'))
                    ->required()
                    ->rows(5)),
                TextInput::make('sort_order')
                    ->label(__('admin_trans.sort_order'))
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('question')
                    ->label(__('admin_trans.question'))
                    ->searchable(),
                TextColumn::make('answer')
                    ->label(__('admin_trans.answer'))
                    ->limit(70),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                TextColumn::make('sort_order')
                    ->label(__('admin_trans.sort_order'))
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
            'index' => ManageFaqs::route('/'),
        ];
    }
}

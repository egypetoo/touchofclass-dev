<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ListContactMessages;
use App\Filament\Resources\ContactMessages\Pages\ViewContactMessage;
use App\Models\ContactMessage;
use App\Services\Marketing\UtmAttribution;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.contact_messages');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.contact_message');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.contact_messages');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label(__('admin_trans.contact_message_name')),
                TextEntry::make('email')
                    ->label(__('admin_trans.email'))
                    ->copyable(),
                TextEntry::make('phone')
                    ->label(__('admin_trans.phone'))
                    ->placeholder('—'),
                TextEntry::make('project_type')
                    ->label(__('admin_trans.contact_project_type'))
                    ->placeholder('—'),
                TextEntry::make('service.title')
                    ->label(__('admin_trans.service_model'))
                    ->placeholder('—'),
                TextEntry::make('utm_source')
                    ->label(__('admin_trans.utm_platform'))
                    ->formatStateUsing(fn (?string $state): string => app(UtmAttribution::class)->platformLabel($state) ?? ($state ?: '—'))
                    ->placeholder('—'),
                TextEntry::make('utm_medium')
                    ->label(__('admin_trans.utm_medium'))
                    ->placeholder('—'),
                TextEntry::make('utm_campaign')
                    ->label(__('admin_trans.utm_campaign'))
                    ->placeholder('—'),
                TextEntry::make('utm_term')
                    ->label(__('admin_trans.utm_term'))
                    ->placeholder('—')
                    ->visible(fn ($record): bool => filled($record->utm_term)),
                TextEntry::make('utm_content')
                    ->label(__('admin_trans.utm_content'))
                    ->placeholder('—')
                    ->visible(fn ($record): bool => filled($record->utm_content)),
                TextEntry::make('landing_page')
                    ->label(__('admin_trans.utm_landing_page'))
                    ->placeholder('—')
                    ->columnSpanFull()
                    ->visible(fn ($record): bool => filled($record->landing_page)),
                TextEntry::make('message')
                    ->label(__('admin_trans.message'))
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_trans.contact_message_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin_trans.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('admin_trans.phone'))
                    ->toggleable(),
                TextColumn::make('project_type')
                    ->label(__('admin_trans.contact_project_type'))
                    ->placeholder('—')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('service.title')
                    ->label(__('admin_trans.service_model'))
                    ->toggleable(),
                TextColumn::make('utm_source')
                    ->label(__('admin_trans.utm_platform'))
                    ->formatStateUsing(fn (?string $state): string => app(UtmAttribution::class)->platformLabel($state) ?? ($state ?: '—'))
                    ->badge()
                    ->placeholder('—')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('utm_campaign')
                    ->label(__('admin_trans.utm_campaign'))
                    ->toggleable()
                    ->placeholder('—'),
                TextColumn::make('message')
                    ->label(__('admin_trans.message'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('service_id')
                    ->label(__('admin_trans.service_model'))
                    ->relationship('service', 'title'),
                SelectFilter::make('utm_source')
                    ->label(__('admin_trans.utm_platform'))
                    ->options(fn (): array => ContactMessage::query()
                        ->whereNotNull('utm_source')
                        ->where('utm_source', '!=', '')
                        ->distinct()
                        ->orderBy('utm_source')
                        ->pluck('utm_source', 'utm_source')
                        ->mapWithKeys(fn (string $source): array => [
                            $source => app(UtmAttribution::class)->platformLabel($source) ?? $source,
                        ])
                        ->all()),
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ListContactMessages::route('/'),
            'view' => ViewContactMessage::route('/{record}'),
        ];
    }
}

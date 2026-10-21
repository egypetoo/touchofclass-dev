<?php

namespace App\Filament\Resources\B2bInquiries;

use App\Filament\Resources\B2bInquiries\Pages\ListB2bInquiries;
use App\Filament\Resources\B2bInquiries\Pages\ViewB2bInquiry;
use App\Models\B2bInquiry;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class B2bInquiryResource extends Resource
{
    protected static ?string $model = B2bInquiry::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.b2b_inquiries');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.b2b_inquiry');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.b2b_inquiries');
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
                TextEntry::make('company_name')
                    ->label(__('admin_trans.company_name')),
                TextEntry::make('first_name')
                    ->label(__('admin_trans.first_name')),
                TextEntry::make('last_name')
                    ->label(__('admin_trans.last_name')),
                TextEntry::make('email')
                    ->label(__('admin_trans.email'))
                    ->copyable(),
                TextEntry::make('phone')
                    ->label(__('admin_trans.phone'))
                    ->copyable(),
                TextEntry::make('street_address')
                    ->label(__('admin_trans.street_address'))
                    ->columnSpanFull(),
                TextEntry::make('apartment_floor')
                    ->label(__('admin_trans.apartment_floor'))
                    ->placeholder('—'),
                TextEntry::make('city')
                    ->label(__('admin_trans.city')),
                TextEntry::make('governorate')
                    ->label(__('admin_trans.governorate')),
                TextEntry::make('postal_code')
                    ->label(__('admin_trans.zip'))
                    ->placeholder('—'),
                TextEntry::make('country')
                    ->label(__('admin_trans.country')),
                TextEntry::make('message')
                    ->label(__('admin_trans.message'))
                    ->placeholder('—')
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
                TextColumn::make('company_name')
                    ->label(__('admin_trans.company_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label(__('admin_trans.first_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('last_name')
                    ->label(__('admin_trans.last_name'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('email')
                    ->label(__('admin_trans.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->label(__('admin_trans.phone'))
                    ->toggleable(),
                TextColumn::make('city')
                    ->label(__('admin_trans.city'))
                    ->toggleable(),
                TextColumn::make('message')
                    ->label(__('admin_trans.message'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
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
            'index' => ListB2bInquiries::route('/'),
            'view' => ViewB2bInquiry::route('/{record}'),
        ];
    }
}

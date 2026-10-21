<?php

namespace App\Filament\Resources\Feedbacks;

use App\Filament\Resources\Feedbacks\Pages\ListFeedback;
use App\Filament\Resources\Feedbacks\Pages\ViewFeedback;
use App\Models\Feedback;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FeedbackResource extends Resource
{
    protected static ?string $model = Feedback::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleLeftRight;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.feedbacks');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.feedback');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.feedbacks');
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
                    ->label(__('admin_trans.name')),
                TextEntry::make('date_of_visit')
                    ->label(__('admin_trans.date_of_visit'))
                    ->date()
                    ->placeholder('—'),
                TextEntry::make('satisfaction')
                    ->label(__('admin_trans.satisfaction'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('site.feedback_'.$state) : '—'),
                TextEntry::make('recommendation')
                    ->label(__('admin_trans.recommendation'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('site.feedback_'.$state) : '—'),
                TextEntry::make('staff_friendliness')
                    ->label(__('admin_trans.staff_friendliness')),
                TextEntry::make('service_speed')
                    ->label(__('admin_trans.service_speed')),
                TextEntry::make('product_quality')
                    ->label(__('admin_trans.product_quality')),
                TextEntry::make('value_for_money')
                    ->label(__('admin_trans.value_for_money')),
                TextEntry::make('hear_about_us')
                    ->label(__('admin_trans.hear_about_us'))
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('improvements')
                    ->label(__('admin_trans.improvements'))
                    ->placeholder('—')
                    ->columnSpanFull(),
                TextEntry::make('additional_comments')
                    ->label(__('admin_trans.additional_comments'))
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
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('satisfaction')
                    ->label(__('admin_trans.satisfaction'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('site.feedback_'.$state) : '—')
                    ->searchable(),
                TextColumn::make('recommendation')
                    ->label(__('admin_trans.recommendation'))
                    ->formatStateUsing(fn (?string $state): string => $state ? __('site.feedback_'.$state) : '—')
                    ->searchable(),
                TextColumn::make('date_of_visit')
                    ->label(__('admin_trans.date_of_visit'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_read')
                    ->label(__('admin_trans.is_read'))
                    ->boolean(),
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
            'index' => ListFeedback::route('/'),
            'view' => ViewFeedback::route('/{record}'),
        ];
    }
}

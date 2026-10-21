<?php

namespace App\Filament\Resources\Testimonials;

use App\Filament\Resources\Testimonials\Pages\ManageTestimonials;
use App\Models\Testimonial;
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

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.testimonials');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.testimonial');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.testimonials');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('video_url')
                    ->label(__('admin_trans.video_url'))
                    ->nullable()
                    ->url()
                    ->helperText(__('admin_trans.testimonial_video_url_help'))
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('image')
                    ->label(__('admin_trans.image'))
                    ->disk(config('media-library.disk_name', 'public'))
                    ->collection('testimonials')
                    ->acceptedFileTypes(['image/webp', 'image/jpeg', 'image/png'])
                    ->image()
                    ->imageEditor()
                    ->maxSize(5120)
                    ->columnSpanFull(),
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
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('testimonials')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('video_url')
                    ->label(__('admin_trans.video_url'))
                    ->searchable()
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? __('admin_trans.watch_video') : '-')
                    ->placeholder('-')
                    ->url(fn (Testimonial $record): ?string => $record->video_url)
                    ->openUrlInNewTab()
                    ->limit(50),
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
            'index' => ManageTestimonials::route('/'),
        ];
    }
}

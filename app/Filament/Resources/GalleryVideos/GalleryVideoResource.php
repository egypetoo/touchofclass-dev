<?php

namespace App\Filament\Resources\GalleryVideos;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\GalleryVideos\Pages\ManageGalleryVideos;
use App\Filament\Support\LocalizedField;
use App\Models\GalleryVideo;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class GalleryVideoResource extends Resource
{
    protected static ?string $model = GalleryVideo::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['title', 'description']),
                Select::make('service_id')
                    ->label(__('admin_trans.service_model'))
                    ->relationship('service', 'title')
                    ->searchable()
                    ->preload()
                    ->nullable(),
                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.title'))
                    ->nullable()),
                LocalizedField::make('description', fn (string $name) => Textarea::make($name)
                    ->label(__('admin_trans.description'))
                    ->nullable()),
                SpatieMediaLibraryFileUpload::make('image')
                    ->label(__('admin_trans.image'))
                    ->disk(config('media-library.disk_name', 'public'))
                    ->collection('gallery_video_images')
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/webp',
                    ])
                    ->maxFiles(1)
                    ->maxSize(5120)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('video_file')
                    ->label(__('admin_trans.video_file'))
                    ->disk(config('media-library.disk_name', 'public'))
                    ->collection('gallery_videos')
                    ->acceptedFileTypes([
                        'video/mp4',
                        'video/webm',
                        'video/ogg',
                    ])
                    ->maxFiles(1)
                    ->maxSize(5120)
                    ->helperText(__('admin_trans.video_file_help'))
                    ->columnSpanFull(),
                TextInput::make('video_url')
                    ->label(__('admin_trans.youtube_link'))
                    ->nullable()
                    ->url()
                    // ->rule('regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\//i')
                    // ->helperText(__('admin_trans.youtube_link_help'))
                    ->columnSpanFull(),
                TextInput::make('sort_order')
                    ->nullable()
                    ->numeric()
                    ->default(0),
                Toggle::make('is_active')
                    ->nullable()
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service.title')
                    ->label(__('admin_trans.service_model'))
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),
                TextColumn::make('description')
                    ->label(__('admin_trans.description'))
                    ->searchable(),
                TextColumn::make('video_url')
                    ->label(__('admin_trans.youtube_link'))
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? __('admin_trans.watch_video') : '-')
                    ->placeholder('-')
                    ->url(fn (GalleryVideo $record): ?string => $record->video_url)
                    ->openUrlInNewTab(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.is_active'))
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label(__('admin_trans.sort_order'))
                    ->numeric()
                    ->searchable()
                    ->sortable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('service_id')
                    ->label(__('admin_trans.service_model'))
                    ->relationship('service', 'title'),
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
            'index' => ManageGalleryVideos::route('/'),
        ];
    }
}

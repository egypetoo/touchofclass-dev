<?php

namespace App\Filament\Resources\Projects;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Projects\Pages\CreateProject;
use App\Filament\Resources\Projects\Pages\EditProject;
use App\Filament\Resources\Projects\Pages\ListProjects;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Project;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|\UnitEnum|null $navigationGroup = 'Media';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.projects_nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.project_model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.projects_nav');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TranslateContentAction::make(['title', 'location', 'area', 'duration', 'features', 'short_text', 'text', 'meta_title', 'meta_desc']),
            Tabs::make('tabs')->tabs([
                Tab::make(__('admin_trans.general'))->schema([
                    LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.title'))
                        ->required()
                        ->maxLength(255)),
                    Select::make('project_category_id')
                        ->label(__('admin_trans.project_category'))
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload()
                        ->nullable(),
                    LocalizedField::make('location', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.location'))
                        ->maxLength(255)),
                    TextInput::make('icon')
                        ->label(__('admin_trans.icon'))
                        ->placeholder('fas fa-futbol')
                        ->maxLength(100),
                    TextInput::make('completed_year')
                        ->label(__('admin_trans.completed_year'))
                        ->maxLength(20),
                    LocalizedField::make('area', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.project_area'))
                        ->maxLength(255)),
                    LocalizedField::make('duration', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.project_duration'))
                        ->maxLength(255)),
                    LocalizedField::make('features', fn (string $name) => Textarea::make($name)
                        ->label(__('admin_trans.project_features'))
                        ->helperText(__('admin_trans.project_features_help'))
                        ->rows(5)
                        ->formatStateUsing(fn ($state): string => is_array($state) ? implode("\n", $state) : (string) $state)
                        ->dehydrateStateUsing(function (?string $state): array {
                            return array_values(array_filter(array_map(
                                static fn (string $line): string => trim($line),
                                preg_split("/\r\n|\r|\n/", (string) $state) ?: []
                            )));
                        })),
                    Toggle::make('is_active')
                        ->label(__('admin_trans.publish_status'))
                        ->default(true),
                    Toggle::make('show_in_home')
                        ->label(__('admin_trans.show_in_home'))
                        ->default(false),
                    TextInput::make('order')
                        ->label(__('admin_trans.order'))
                        ->numeric()
                        ->default(0)
                        ->required(),
                ]),
                Tab::make(__('admin_trans.content'))->schema([
                    LocalizedField::make('short_text', fn (string $name) => Textarea::make($name)
                        ->label(__('admin_trans.short_text'))
                        ->rows(3)),
                    LocalizedField::make('text', fn (string $name) => RichEditor::make($name)
                        ->label(__('admin_trans.text'))
                        ->helperText(__('admin_trans.content_call_shortcode_help'))
                        ->fileAttachmentsDisk('public')
                        ->fileAttachmentsDirectory('project-attachments')),
                ]),
                Tab::make(__('admin_trans.images'))->schema([
                    SpatieMediaLibraryFileUpload::make('cover')
                        ->label(__('admin_trans.cover_image'))
                        ->collection('cover')
                        ->disk(config('media-library.disk_name', 'public'))
                        ->acceptedFileTypes(['image/webp', 'image/jpeg', 'image/png'])
                        ->maxSize(5120)
                        ->image()
                        ->imageEditor()
                        ->columnSpanFull(),
                    MultiImageUpload::make('gallery')
                        ->label(__('admin_trans.gallery_images'))
                        ->collection('gallery'),
                ]),
                Tab::make(__('admin_trans.seo'))->schema([
                    LocalizedField::make('meta_title', fn (string $name) => TextInput::make($name)
                        ->label(__('admin_trans.meta_title'))
                        ->maxLength(255)),
                    LocalizedField::make('meta_desc', fn (string $name) => Textarea::make($name)
                        ->label(__('admin_trans.meta_desc'))
                        ->rows(3)),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('cover')
                    ->label(__('admin_trans.cover_image'))
                    ->collection('cover'),
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),
                TextColumn::make('category.name')
                    ->label(__('admin_trans.project_category'))
                    ->toggleable(),
                TextColumn::make('location')
                    ->label(__('admin_trans.location'))
                    ->toggleable(),
                TextColumn::make('order')
                    ->label(__('admin_trans.order'))
                    ->sortable(),
                ToggleColumn::make('show_in_home')
                    ->label(__('admin_trans.show_in_home')),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
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
            'index' => ListProjects::route('/'),
            'create' => CreateProject::route('/create'),
            'edit' => EditProject::route('/{record}/edit'),
        ];
    }
}

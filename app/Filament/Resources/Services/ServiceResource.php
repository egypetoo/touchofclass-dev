<?php

namespace App\Filament\Resources\Services;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Filament\Resources\Services\Pages\ListServices;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Service as ServiceModel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
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

class ServiceResource extends Resource
{
    protected static ?string $model = ServiceModel::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWrenchScrewdriver;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.services_nav');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.service_model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.services_nav');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['title', 'label', 'short_text', 'text', 'meta_title', 'meta_desc']),
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('admin_trans.general'))
                            ->schema([
                                Select::make('parent_id')
                                    ->label(__('admin_trans.parent_service'))
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'title',
                                        modifyQueryUsing: fn ($query) => $query->whereNull('parent_id')->orderBy('order'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.title'))
                                    ->required()
                                    ->maxLength(255)),
                                LocalizedField::make('label', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.service_label'))
                                    ->maxLength(255)),
                                Toggle::make('is_active')
                                    ->label(__('admin_trans.publish_status'))
                                    ->default(true)
                                    ->required(),
                                TextInput::make('order')
                                    ->label(__('admin_trans.order'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Tab::make(__('admin_trans.content'))
                            ->schema([
                                LocalizedField::make('short_text', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.service_short_text'))
                                    ->rows(3)),
                                LocalizedField::make('text', fn (string $name) => RichEditor::make($name)
                                    ->label(__('admin_trans.service_text'))
                                    ->helperText(__('admin_trans.content_call_shortcode_help'))
                                    ->default(null)
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('service-attachments')),
                            ]),
                        Tab::make(__('admin_trans.images'))
                            ->schema([
                                MultiImageUpload::make('images')
                                    ->label(__('admin_trans.images'))
                                    ->helperText(__('admin_trans.service_images_hint').' '.__('admin_trans.multi_image_upload_help'))
                                    ->collection('services'),
                            ]),
                        Tab::make(__('admin_trans.seo'))
                            ->schema([
                                LocalizedField::make('slug', fn (string $name) => TextInput::make($name)
                                    ->hiddenOn('create')
                                    ->required()
                                    ->label(__('admin_trans.slug'))),
                                LocalizedField::make('meta_title', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.meta_title'))
                                    ->maxLength(255)),
                                LocalizedField::make('meta_desc', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.meta_desc'))
                                    ->rows(3)),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('services')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),
                TextColumn::make('parent.title')
                    ->label(__('admin_trans.parent_service'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('children_count')
                    ->counts('children')
                    ->label(__('admin_trans.sub_services_count'))
                    ->toggleable(),
                TextColumn::make('slug')
                    ->label(__('admin_trans.slug'))
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('short_text')
                    ->label(__('admin_trans.service_short_text'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('order')
            ->reorderable('order')
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
            'index' => ListServices::route('/'),
            'create' => CreateService::route('/create'),
            'edit' => EditService::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\Sections;

use App\Enums\PageEnum;
use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Sections\Pages\ManageSections;
use App\Filament\Support\LocalizedField;
use App\Models\Section;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

use function Filament\Support\original_request;

class SectionResource extends Resource
{
    /**
     * Navigation group key registered in {@see AdminPanelProvider}.
     */
    public const NAVIGATION_GROUP_KEY = 'sections';

    protected static ?string $model = Section::class;

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.sections_management');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.section');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.sections_management');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('type')
                    ->label(__('admin_trans.section_type'))
                    ->options(function (Get $get, ManageSections $livewire): array {
                        $pageValue = $get('page') ?? $livewire->page ?? PageEnum::HOME;

                        $page = $pageValue instanceof PageEnum
                            ? $pageValue
                            : (PageEnum::tryFrom((string) $pageValue) ?? PageEnum::HOME);

                        return $page->sectionTypeOptions();
                    })
                    ->required()
                    ->disabled(fn (string $operation) => $operation === 'edit'),

                Select::make('page')
                    ->label(__('admin_trans.page'))
                    ->options(PageEnum::class)
                    ->required()
                    ->default(fn (ManageSections $livewire): string => $livewire->page ?? PageEnum::HOME->value)
                    ->disabled(fn (ManageSections $livewire): bool => filled($livewire->page))
                    ->dehydrated(),

                TranslateContentAction::make(['title', 'second_title', 'sub_title', 'text', 'button_text']),

                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.title'))
                    ->default(null)),
                LocalizedField::make('second_title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.second_title'))
                    ->default(null)),
                LocalizedField::make('sub_title', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.sub_title'))
                    ->default(null)),
                SpatieMediaLibraryFileUpload::make('image')
                    ->disk(config('media-library.disk_name', 'public'))
                    ->label(__('admin_trans.image'))
                    ->collection('sections')
                    ->columnSpanFull(),

                TextInput::make('alt_image')
                    ->label(__('admin_trans.alt_image'))
                    ->columnSpanFull(),

                LocalizedField::make('text', fn (string $name) => RichEditor::make($name)
                    ->label(__('admin_trans.text'))
                    ->helperText(__('admin_trans.content_call_shortcode_help'))
                    ->default(null)
                    ->fileAttachmentsDisk('public')
                    ->fileAttachmentsDirectory('blog-images')),
                LocalizedField::make('button_text', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.button_text'))
                    ->default(null)),
                TextInput::make('button_link_url')
                    ->label(__('admin_trans.button_link_url'))
                    ->default(null),

                TextInput::make('order')
                    ->label(__('admin_trans.order'))
                    ->required()
                    ->numeric()
                    ->default(0),

                Toggle::make('is_active')
                    ->label(__('admin_trans.publish_status'))
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('type')
                    ->label(__('admin_trans.section_type'))
                    ->searchable(),

                TextColumn::make('page')
                    ->label(__('admin_trans.page'))
                    ->searchable(),

                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('sections')
                    ->disk(config('media-library.disk_name', 'public')),

                TextColumn::make('order')
                    ->label(__('admin_trans.order'))
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
                // DeleteAction::make(),
                ViewAction::make(),
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
            'index' => ManageSections::route('/'),
        ];
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        if (! static::hasPage('index')) {
            return [];
        }

        $baseSort = static::getNavigationSort() ?? 0;

        $items = [];

        foreach (PageEnum::cases() as $index => $page) {
            if ($page->sectionTypes() === []) {
                continue;
            }

            $items[] = NavigationItem::make($page->getLabel())
                ->group(static::NAVIGATION_GROUP_KEY)
                ->url(static::getUrl(parameters: ['page' => $page->value]))
                ->sort($baseSort + $index)
                ->isActiveWhen(fn () => original_request()->routeIs(static::getNavigationItemActiveRoutePattern()) && original_request()->query('page') === $page->value);
        }

        return $items;
    }
}

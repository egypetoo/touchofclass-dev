<?php

namespace App\Filament\Resources\Categories;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Categories\Pages\ManageCategories;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Category;
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
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'categories';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.categories_management');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.category');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.categories_management');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema

            ->components([
                TranslateContentAction::make(['name', 'short_text', 'text', 'meta_title', 'meta_desc']),

                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('admin_trans.general'))
                            ->schema([
                                Select::make('parent_id')
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->orderBy('order'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->default(null)
                                    ->label(__('admin_trans.parent_category'))
                                    ->helperText(__('admin_trans.parent_category_help')),

                                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.name'))
                                    ->required()),
                                TextInput::make('order')
                                    ->label(__('admin_trans.order'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ]),
                        Tab::make(__('admin_trans.content'))
                            ->schema([
                                LocalizedField::make('short_text', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.short_text'))
                                    ->default(null)),
                                LocalizedField::make('text', fn (string $name) => RichEditor::make($name)
                                    ->label(__('admin_trans.text'))
                                    ->helperText(__('admin_trans.content_call_shortcode_help'))
                                    ->default(null)),
                            ]),
                        Tab::make(__('admin_trans.images'))
                            ->schema([
                                MultiImageUpload::make('images')
                                    ->collection('categories')
                                    ->label(__('admin_trans.images')),
                            ]),
                        Tab::make(__('admin_trans.display_options'))
                            ->schema([
                                Grid::make()
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('is_active')
                                            ->label(__('admin_trans.publish_status'))
                                            ->required()
                                            ->columnSpan(1),
                                        Toggle::make('show_in_navbar')
                                            ->label(__('admin_trans.show_in_navbar'))
                                            ->columnSpan(1),

                                        Toggle::make('show_in_footer')
                                            ->label(__('admin_trans.show_in_footer'))
                                            ->default(false)
                                            ->columnSpan(1),

                                        // Toggle::make('is_main_section')
                                        //     ->label(__('admin_trans.is_main_section'))
                                        //     ->default(false)
                                        //     ->columnSpan(1),
                                    ]),
                            ]),

                        Tab::make(__('admin_trans.seo'))
                            ->schema([
                                LocalizedField::make('slug', fn (string $name) => TextInput::make($name)
                                    ->hiddenOn('create')
                                    ->label(__('admin_trans.slug'))),
                                LocalizedField::make('meta_title', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.meta_title'))
                                    ->maxLength(255)),
                                LocalizedField::make('meta_desc', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.meta_desc'))
                                    ->rows(3)),
                            ]),
                    ])
                    ->columnSpan(12),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),

                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('categories')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('parent.name')
                    ->label(__('admin_trans.parent_category'))
                    ->searchable(),
                TextColumn::make('children_count')
                    ->label(__('admin_trans.children_count'))
                    ->counts('children'),
                TextColumn::make('products_count')
                    ->label(__('admin_trans.products_count'))
                    ->counts('products'),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),

                ToggleColumn::make('is_active'),
                ToggleColumn::make('show_in_navbar')
                    ->label(__('admin_trans.show_in_navbar')),
                ToggleColumn::make('show_in_footer')
                    ->label(__('admin_trans.show_in_footer')),

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
                DeleteAction::make(),
                // ->visible(fn (Category $record): bool => ! $record->isManufacturing()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    // ->before(function (Collection $records): void {
                    //     if ($records->contains(fn (Category $record): bool => $record->isManufacturing())) {
                    //         throw ValidationException::withMessages([
                    //             'category' => __('admin_trans.cannot_delete_manufacturing_category'),
                    //         ]);
                    //     }
                    // }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCategories::route('/'),
        ];
    }
}

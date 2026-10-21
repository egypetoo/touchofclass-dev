<?php

namespace App\Filament\Resources\Products;

use App\Enums\ProductStatusEnum;
use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Products\Pages\CreateProduct;
use App\Filament\Resources\Products\Pages\EditProduct;
use App\Filament\Resources\Products\Pages\ListProducts;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Product;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Validation\Rules\Unique;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static string|\UnitEnum|null $navigationGroup = 'Products';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.products');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.product');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.products');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['name', 'short_description', 'description', 'meta_title', 'meta_desc']),
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('admin_trans.general'))
                            ->schema([
                                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.name'))
                                    ->required()
                                    ->maxLength(255)),
                                Select::make('category_id')
                                    ->label(__('admin_trans.category'))
                                    ->relationship(
                                        name: 'category',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('order'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->nullable(),
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('code')
                                            ->label(__('admin_trans.product_code'))
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule) => $rule->withoutTrashed()),
                                        TextInput::make('price')
                                            ->label(__('admin_trans.price'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                        TextInput::make('quantity')
                                            ->label(__('admin_trans.quantity'))
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0),
                                    ]),
                                Grid::make()
                                    ->columns(3)
                                    ->schema([
                                        Select::make('status')
                                            ->label(__('admin_trans.product_status'))
                                            ->options(static::statusOptions())
                                            ->nullable(),
                                        TextInput::make('sort_order')
                                            ->label(__('admin_trans.sort_order'))
                                            ->integer()
                                            ->minValue(0)
                                            ->default(0),
                                        Toggle::make('is_active')
                                            ->label(__('admin_trans.publish_status'))
                                            ->default(true)
                                            ->inline(false),
                                    ]),
                            ]),
                        Tab::make(__('admin_trans.content'))
                            ->schema([
                                LocalizedField::make('short_description', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.short_description'))
                                    ->rows(3)),
                                LocalizedField::make('description', fn (string $name) => RichEditor::make($name)
                                    ->label(__('admin_trans.description'))
                                    ->helperText(__('admin_trans.content_call_shortcode_help'))
                                    ->default(null)
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('product-attachments')),
                            ]),
                        Tab::make(__('admin_trans.images'))
                            ->schema([
                                MultiImageUpload::make('images')
                                    ->label(__('admin_trans.images'))
                                    ->helperText(__('admin_trans.product_images_main_hint'))
                                    ->collection('products'),
                            ]),
                        Tab::make(__('admin_trans.catalog_presentation'))
                            ->schema([
                                TagsInput::make('catalog_sizes')
                                    ->label(__('admin_trans.catalog_sizes'))
                                    ->helperText(__('admin_trans.catalog_sizes_help'))
                                    ->columnSpanFull(),
                                Repeater::make('catalog_colors')
                                    ->label(__('admin_trans.catalog_colors'))
                                    ->schema([
                                        ColorPicker::make('hex')
                                            ->label(__('admin_trans.color'))
                                            ->required(),
                                        TextInput::make('label')
                                            ->label(__('admin_trans.color_label'))
                                            ->maxLength(100),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->defaultItems(0)
                                    ->columnSpanFull(),
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
                    ->collection('products')
                    ->limit(1)
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('code')
                    ->label(__('admin_trans.product_code'))
                    ->searchable()
                    ->placeholder('—'),
                TextColumn::make('category.name')
                    ->label(__('admin_trans.category'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('price')
                    ->label(__('admin_trans.price'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('admin_trans.quantity'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('admin_trans.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return Builder<Product>
     */
    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }

    /**
     * @return array<string, string>
     */
    public static function statusOptions(): array
    {
        return collect(ProductStatusEnum::cases())
            ->mapWithKeys(fn (ProductStatusEnum $status): array => [
                $status->value => __('admin_trans.product_status_'.strtolower($status->name)),
            ])
            ->all();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProducts::route('/'),
            'create' => CreateProduct::route('/create'),
            'edit' => EditProduct::route('/{record}/edit'),
        ];
    }
}

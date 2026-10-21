<?php

namespace App\Filament\Resources\Blogs;

use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Blogs\Pages\ManageBlogs;
use App\Filament\Support\LocalizedField;
use App\Filament\Support\MultiImageUpload;
use App\Models\Blog;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
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
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|\UnitEnum|null $navigationGroup = 'Blog';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.blogs');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.blog');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.blogs');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['title', 'excerpt', 'content', 'meta_title', 'meta_desc']),
                Tabs::make('tabs')
                    ->tabs([
                        Tab::make(__('admin_trans.general'))
                            ->schema([
                                Select::make('blog_category_id')
                                    ->relationship('blogCategory', 'name')
                                    ->label(__('admin_trans.blog_category'))
                                    ->searchable()
                                    ->preload(),
                                LocalizedField::make('title', fn (string $name) => TextInput::make($name)
                                    ->label(__('admin_trans.title'))
                                    ->required()),
                                TextInput::make('order')
                                    ->label(__('admin_trans.order'))
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                                Toggle::make('is_active')
                                    ->label(__('admin_trans.publish_status'))
                                    ->required(),
                                DateTimePicker::make('published_at')
                                    ->label(__('admin_trans.published_at'))
                                    ->seconds(false),
                            ]),
                        Tab::make(__('admin_trans.content'))
                            ->schema([
                                LocalizedField::make('excerpt', fn (string $name) => Textarea::make($name)
                                    ->label(__('admin_trans.excerpt'))
                                    ->rows(3)
                                    ->default(null)),
                                LocalizedField::make('content', fn (string $name) => RichEditor::make($name)
                                    ->label(__('admin_trans.text'))
                                    ->helperText(__('admin_trans.content_call_shortcode_help'))
                                    ->default(null)
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('blog-attachments')),
                            ]),
                        Tab::make(__('admin_trans.images'))
                            ->schema([
                                MultiImageUpload::make('images')
                                    ->collection('blogs')
                                    ->label(__('admin_trans.featured_image')),
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
                TextColumn::make('title')
                    ->label(__('admin_trans.title'))
                    ->searchable(),
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('blogs')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('blogCategory.name')
                    ->label(__('admin_trans.blog_category'))
                    ->searchable(),
                TextColumn::make('published_at')
                    ->label(__('admin_trans.published_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('order')
                    ->numeric()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
                TextColumn::make('created_at')
                    ->label(__('admin_trans.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order')
            ->reorderable('order')
            ->filters([
                SelectFilter::make('blog_category_id')
                    ->relationship('blogCategory', 'name')
                    ->label(__('admin_trans.blog_category'))
                    ->searchable()
                    ->preload(),
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
            'index' => ManageBlogs::route('/'),
        ];
    }
}

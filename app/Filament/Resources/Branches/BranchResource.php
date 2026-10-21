<?php

namespace App\Filament\Resources\Branches;

use App\Enums\BranchTypeEnum;
use App\Filament\Actions\TranslateContentAction;
use App\Filament\Resources\Branches\Pages\CreateBranch;
use App\Filament\Resources\Branches\Pages\EditBranch;
use App\Filament\Resources\Branches\Pages\ListBranches;
use App\Filament\Support\LocalizedField;
use App\Models\Branch;
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
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $tenantOwnershipRelationshipName = 'organization';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMapPin;

    protected static string|\UnitEnum|null $navigationGroup = 'Company';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('admin_trans.branches');
    }

    public static function getModelLabel(): string
    {
        return __('admin_trans.branch');
    }

    public static function getPluralModelLabel(): string
    {
        return __('admin_trans.branches');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslateContentAction::make(['name', 'address', 'working_hours']),
                LocalizedField::make('name', fn (string $name) => TextInput::make($name)
                    ->label(__('admin_trans.name'))
                    ->required()
                    ->maxLength(255)),
                Select::make('type')
                    ->label(__('admin_trans.branch_type'))
                    ->options(BranchTypeEnum::class)
                    ->required()
                    ->native(false),
                SpatieMediaLibraryFileUpload::make('image')
                    ->disk(config('media-library.disk_name', 'public'))
                    ->label(__('admin_trans.image'))
                    ->collection('branches')
                    ->image()
                    ->imageEditor()
                    ->acceptedFileTypes(['image/webp', 'image/jpeg', 'image/png'])
                    ->maxSize(5120),
                // ->columnSpanFull(),
                LocalizedField::make('address', fn (string $name) => Textarea::make($name)
                    ->label(__('admin_trans.address'))
                    ->rows(3)),
                TextInput::make('map_url')
                    ->label(__('admin_trans.map_url'))
                    ->url()
                    ->maxLength(2048),
                Textarea::make('map_embed')
                    ->label(__('admin_trans.map_embed'))
                    ->helperText(__('admin_trans.map_embed_help'))
                    ->rows(4)
                    ->columnSpanFull(),
                TextInput::make('phone_1_country_code')
                    ->label(__('admin_trans.phone_1_country_code'))
                    ->maxLength(16)
                    ->placeholder('+966'),
                TextInput::make('phone_1')
                    ->label(__('admin_trans.phone_1'))
                    ->tel()
                    ->maxLength(32),
                TextInput::make('phone_2_country_code')
                    ->label(__('admin_trans.phone_2_country_code'))
                    ->maxLength(16)
                    ->placeholder('+966'),
                TextInput::make('phone_2')
                    ->label(__('admin_trans.phone_2'))
                    ->tel()
                    ->maxLength(32),
                LocalizedField::make('working_hours', fn (string $name) => RichEditor::make($name)
                    ->label(__('admin_trans.working_hours'))
                    ->default(null)),
                TextInput::make('email')
                    ->label(__('admin_trans.email'))
                    ->email()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label(__('admin_trans.publish_status'))
                    ->default(true)
                    ->required(),
                TextInput::make('order')
                    ->label(__('admin_trans.order'))
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderByRaw(
                'CASE WHEN '.DB::getTablePrefix().'branches.type = ? THEN 0 ELSE 1 END',
                [BranchTypeEnum::Main->value]
            )->orderBy('order')->orderBy('id'))
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')
                    ->label(__('admin_trans.image'))
                    ->collection('branches')
                    ->disk(config('media-library.disk_name', 'public')),
                TextColumn::make('name')
                    ->label(__('admin_trans.name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('admin_trans.branch_type'))
                    ->formatStateUsing(function ($state): string {
                        $enum = $state instanceof BranchTypeEnum
                            ? $state
                            : BranchTypeEnum::tryFrom((string) $state);

                        return $enum?->getLabel() ?? '';
                    })
                    ->badge()
                    ->color(function ($state): string {
                        $enum = $state instanceof BranchTypeEnum
                            ? $state
                            : BranchTypeEnum::tryFrom((string) $state);

                        return match ($enum) {
                            BranchTypeEnum::Main => 'warning',
                            BranchTypeEnum::Other => 'gray',
                            default => 'gray',
                        };
                    }),
                TextColumn::make('email')
                    ->label(__('admin_trans.email'))
                    ->searchable(),
                TextColumn::make('phone_1')
                    ->label(__('admin_trans.phone_1'))
                    ->formatStateUsing(function (Branch $record): ?string {
                        if ($record->phone_1 === null || $record->phone_1 === '') {
                            return null;
                        }

                        $code = $record->phone_1_country_code;

                        return ($code !== null && $code !== '' ? $code.' ' : '').$record->phone_1;
                    }),
                ToggleColumn::make('is_active')
                    ->label(__('admin_trans.publish_status')),
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
            'index' => ListBranches::route('/'),
            'create' => CreateBranch::route('/create'),
            'edit' => EditBranch::route('/{record}/edit'),
        ];
    }
}

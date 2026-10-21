<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Categories\CategoryResource;
use App\Services\Spreadsheet\Definitions\CategorySpreadsheetDefinition;
use BackedEnum;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Icons\Heroicon;

class ManageCategories extends ManageRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = CategoryResource::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?int $navigationSort = 3;

    protected static function spreadsheetDefinition(): string
    {
        return CategorySpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Services\ServiceResource;
use App\Services\Spreadsheet\Definitions\ServiceSpreadsheetDefinition;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServices extends ListRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = ServiceResource::class;

    protected static function spreadsheetDefinition(): string
    {
        return ServiceSpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }
}

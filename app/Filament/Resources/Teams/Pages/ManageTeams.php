<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Teams\TeamResource;
use App\Services\Spreadsheet\Definitions\TeamSpreadsheetDefinition;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTeams extends ManageRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = TeamResource::class;

    protected static function spreadsheetDefinition(): string
    {
        return TeamSpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }
}

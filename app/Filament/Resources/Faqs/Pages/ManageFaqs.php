<?php

namespace App\Filament\Resources\Faqs\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Faqs\FaqResource;
use App\Services\Spreadsheet\Definitions\FaqSpreadsheetDefinition;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFaqs extends ManageRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = FaqResource::class;

    protected static function spreadsheetDefinition(): string
    {
        return FaqSpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }
}

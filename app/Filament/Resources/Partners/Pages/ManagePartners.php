<?php

namespace App\Filament\Resources\Partners\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Partners\PartnerResource;
use App\Services\Spreadsheet\Definitions\PartnerSpreadsheetDefinition;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ManageRecords;

class ManagePartners extends ManageRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = PartnerResource::class;

    protected static function spreadsheetDefinition(): string
    {
        return PartnerSpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['organization_id'] = Filament::getTenant()?->getKey();

        return $data;
    }
}

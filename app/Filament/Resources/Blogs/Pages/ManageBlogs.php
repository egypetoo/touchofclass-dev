<?php

namespace App\Filament\Resources\Blogs\Pages;

use App\Filament\Concerns\InteractsWithSpreadsheet;
use App\Filament\Resources\Blogs\BlogResource;
use App\Services\Spreadsheet\Definitions\BlogSpreadsheetDefinition;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogs extends ManageRecords
{
    use InteractsWithSpreadsheet;

    protected static string $resource = BlogResource::class;

    protected static function spreadsheetDefinition(): string
    {
        return BlogSpreadsheetDefinition::class;
    }

    protected function getHeaderActions(): array
    {
        return [
            ...$this->getSpreadsheetHeaderActions(),
            CreateAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\BeforeAfters\Pages;

use App\Filament\Resources\BeforeAfters\BeforeAfterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBeforeAfters extends ManageRecords
{
    protected static string $resource = BeforeAfterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

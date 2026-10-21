<?php

namespace App\Filament\Resources\GalleryVideos\Pages;

use App\Filament\Resources\GalleryVideos\GalleryVideoResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGalleryVideos extends ManageRecords
{
    protected static string $resource = GalleryVideoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}

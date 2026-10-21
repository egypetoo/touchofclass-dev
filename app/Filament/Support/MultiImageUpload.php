<?php

namespace App\Filament\Support;

use Filament\Actions\Action;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class MultiImageUpload
{
    /**
     * Shared multi-image upload: grid layout + reorder + clear-all action.
     */
    public static function make(string $name): SpatieMediaLibraryFileUpload
    {
        return SpatieMediaLibraryFileUpload::make($name)
            ->disk(config('media-library.disk_name', 'public'))
            ->acceptedFileTypes([
                'image/webp',
                'image/jpeg',
                'image/png',
            ])
            ->maxSize(5120)
            ->multiple()
            ->reorderable()
            ->appendFiles()
            ->panelLayout('grid')
            ->imagePreviewHeight('160')
            ->image()
            ->imageEditor()
            ->columnSpanFull()
            ->helperText(__('admin_trans.multi_image_upload_help'))
            ->hintAction(static::removeAllAction());
    }

    protected static function removeAllAction(): Action
    {
        return Action::make('removeAllImages')
            ->label(__('admin_trans.remove_all_images'))
            ->icon(Heroicon::OutlinedTrash)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__('admin_trans.remove_all_images'))
            ->modalDescription(__('admin_trans.remove_all_images_confirm'))
            ->action(function (SpatieMediaLibraryFileUpload $component, Set $set, ?Model $record): void {
                $set($component->getStatePath(isAbsolute: false), []);

                $collection = $component->getCollection();

                if ($record instanceof HasMedia && filled($collection)) {
                    $record->clearMediaCollection($collection);
                }

                Notification::make()
                    ->success()
                    ->title(__('admin_trans.remove_all_images_done'))
                    ->send();
            });
    }
}

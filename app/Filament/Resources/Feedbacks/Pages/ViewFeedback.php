<?php

namespace App\Filament\Resources\Feedbacks\Pages;

use App\Filament\Resources\Feedbacks\FeedbackResource;
use Filament\Resources\Pages\ViewRecord;

class ViewFeedback extends ViewRecord
{
    protected static string $resource = FeedbackResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (! $this->record->is_read) {
            $this->record->update(['is_read' => true]);
        }
    }
}

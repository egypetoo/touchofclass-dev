<?php

namespace App\Filament\Resources\Sections\Pages;

use App\Enums\PageEnum;
use App\Filament\Resources\Sections\SectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Livewire\Attributes\Url;

class ManageSections extends ManageRecords
{
    protected static string $resource = SectionResource::class;

    #[Url(as: 'page')]
    public ?string $page = null;

    public function mount(): void
    {
        if (blank($this->page)) {
            $this->redirect(static::getResource()::getUrl(parameters: [
                'page' => PageEnum::HOME->value,
            ]));

            return;
        }

        if (PageEnum::tryFrom($this->page) === null) {
            $this->redirect(static::getResource()::getUrl(parameters: [
                'page' => PageEnum::HOME->value,
            ]));

            return;
        }

        parent::mount();
    }

    public function getTitle(): string|Htmlable
    {
        $enum = PageEnum::tryFrom((string) $this->page);

        if ($enum) {
            return $enum->getLabel().' — '.static::getResource()::getPluralModelLabel();
        }

        return parent::getTitle();
    }

    protected function getTableQuery(): Builder|Relation|null
    {
        $query = parent::getTableQuery();

        if (filled($this->page)) {
            $query->where('page', $this->page);
        }

        return $query;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->fillForm(fn (): array => [
                    'page' => $this->page ?? PageEnum::HOME->value,
                ]),
        ];
    }
}

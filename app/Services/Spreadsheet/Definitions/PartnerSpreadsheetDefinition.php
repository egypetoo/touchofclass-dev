<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Partner;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class PartnerSpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Name (EN)', 'Name (AR)', 'URL',
            'Status (1=Active)', 'Order',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'Partner name', 'اسم الشريك',
            'https://example.com',
            '1', '1',
        ];
    }

    public function exportRows(): array
    {
        return Partner::query()->orderBy('order')->get()->map(fn (Partner $partner) => [
            $partner->getTranslation('name', 'en'),
            $partner->getTranslation('name', 'ar'),
            $partner->url,
            $partner->is_active ? '1' : '0',
            $partner->order,
        ])->all();
    }

    public function importRow(array $row, int $rowNumber): array
    {
        $nameAr = trim((string) ($row[1] ?? ''));
        if ($nameAr === '') {
            return ['inserted' => 0, 'updated' => 0, 'errors' => [__('admin_trans.import_row_missing_title_ar', ['row' => $rowNumber])]];
        }

        $payload = [
            'name' => ['en' => trim((string) ($row[0] ?? '')), 'ar' => $nameAr],
            'url' => trim((string) ($row[2] ?? '')) ?: null,
            'is_active' => in_array(trim((string) ($row[3] ?? '1')), ['1', 'true', 'yes'], true),
            'order' => (int) ($row[4] ?? 0),
        ];

        $existing = Partner::query()->where('name->ar', $nameAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Partner::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'partners';
    }
}

<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Service;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class ServiceSpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Title (EN)', 'Title (AR)',
            'Short Text (EN)', 'Short Text (AR)',
            'Text (EN)', 'Text (AR)',
            'Slug (EN)', 'Slug (AR)',
            'Meta Title (EN)', 'Meta Title (AR)',
            'Meta Desc (EN)', 'Meta Desc (AR)',
            'Status (1=Active)', 'Order',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'Dental Cleaning', 'تنظيف الأسنان',
            'Short description', 'وصف قصير',
            'Full description', 'وصف كامل',
            'dental-cleaning', 'تنظيف-الاسنان',
            'Meta title', 'عنوان ميتا',
            'Meta description', 'وصف ميتا',
            '1', '1',
        ];
    }

    public function exportRows(): array
    {
        return Service::query()->orderBy('order')->get()->map(fn (Service $service) => [
            $service->getTranslation('title', 'en'),
            $service->getTranslation('title', 'ar'),
            $service->getTranslation('short_text', 'en'),
            $service->getTranslation('short_text', 'ar'),
            $service->getTranslation('text', 'en'),
            $service->getTranslation('text', 'ar'),
            $service->getTranslation('slug', 'en'),
            $service->getTranslation('slug', 'ar'),
            $service->getTranslation('meta_title', 'en'),
            $service->getTranslation('meta_title', 'ar'),
            $service->getTranslation('meta_desc', 'en'),
            $service->getTranslation('meta_desc', 'ar'),
            $service->is_active ? '1' : '0',
            $service->order,
        ])->all();
    }

    public function importRow(array $row, int $rowNumber): array
    {
        $titleAr = trim((string) ($row[1] ?? ''));
        if ($titleAr === '') {
            return ['inserted' => 0, 'updated' => 0, 'errors' => [__('admin_trans.import_row_missing_title_ar', ['row' => $rowNumber])]];
        }

        $payload = [
            'title' => ['en' => trim((string) ($row[0] ?? '')), 'ar' => $titleAr],
            'short_text' => ['en' => trim((string) ($row[2] ?? '')), 'ar' => trim((string) ($row[3] ?? ''))],
            'text' => ['en' => trim((string) ($row[4] ?? '')), 'ar' => trim((string) ($row[5] ?? ''))],
            'slug' => ['en' => trim((string) ($row[6] ?? '')), 'ar' => trim((string) ($row[7] ?? ''))],
            'meta_title' => ['en' => trim((string) ($row[8] ?? '')), 'ar' => trim((string) ($row[9] ?? ''))],
            'meta_desc' => ['en' => trim((string) ($row[10] ?? '')), 'ar' => trim((string) ($row[11] ?? ''))],
            'is_active' => in_array(trim((string) ($row[12] ?? '1')), ['1', 'true', 'yes'], true),
            'order' => (int) ($row[13] ?? 0),
        ];

        $existing = Service::query()->where('title->ar', $titleAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Service::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'services';
    }
}

<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Team;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class TeamSpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Name (EN)', 'Name (AR)',
            'Title (EN)', 'Title (AR)',
            'Bio (EN)', 'Bio (AR)',
            'Status (1=Active)', 'Order',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'Dr. Ahmed', 'د. أحمد',
            'Dentist', 'طبيب أسنان',
            'Short bio', 'نبذة مختصرة',
            '1', '1',
        ];
    }

    public function exportRows(): array
    {
        return Team::query()->orderBy('order')->get()->map(fn (Team $team) => [
            $team->getTranslation('name', 'en'),
            $team->getTranslation('name', 'ar'),
            $team->getTranslation('title', 'en'),
            $team->getTranslation('title', 'ar'),
            $team->getTranslation('bio', 'en'),
            $team->getTranslation('bio', 'ar'),
            $team->is_active ? '1' : '0',
            $team->order,
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
            'title' => ['en' => trim((string) ($row[2] ?? '')), 'ar' => trim((string) ($row[3] ?? ''))],
            'bio' => ['en' => trim((string) ($row[4] ?? '')), 'ar' => trim((string) ($row[5] ?? ''))],
            'is_active' => in_array(trim((string) ($row[6] ?? '1')), ['1', 'true', 'yes'], true),
            'order' => (int) ($row[7] ?? 0),
        ];

        $existing = Team::query()->where('name->ar', $nameAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Team::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'teams';
    }
}

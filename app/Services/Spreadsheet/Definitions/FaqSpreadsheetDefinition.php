<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Faq;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class FaqSpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Question (EN)', 'Question (AR)',
            'Answer (EN)', 'Answer (AR)',
            'Status (1=Active)', 'Sort Order',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'How can I book?', 'كيف أحجز؟',
            'You can book online or by phone.', 'يمكنك الحجز عبر الموقع أو الهاتف.',
            '1', '1',
        ];
    }

    public function exportRows(): array
    {
        return Faq::query()->orderBy('sort_order')->get()->map(fn (Faq $faq) => [
            $faq->getTranslation('question', 'en'),
            $faq->getTranslation('question', 'ar'),
            $faq->getTranslation('answer', 'en'),
            $faq->getTranslation('answer', 'ar'),
            $faq->is_active ? '1' : '0',
            $faq->sort_order,
        ])->all();
    }

    public function importRow(array $row, int $rowNumber): array
    {
        $questionAr = trim((string) ($row[1] ?? ''));
        if ($questionAr === '') {
            return ['inserted' => 0, 'updated' => 0, 'errors' => [__('admin_trans.import_row_missing_title_ar', ['row' => $rowNumber])]];
        }

        $payload = [
            'question' => ['en' => trim((string) ($row[0] ?? '')), 'ar' => $questionAr],
            'answer' => ['en' => trim((string) ($row[2] ?? '')), 'ar' => trim((string) ($row[3] ?? ''))],
            'is_active' => in_array(trim((string) ($row[4] ?? '1')), ['1', 'true', 'yes'], true),
            'sort_order' => (int) ($row[5] ?? 0),
        ];

        $existing = Faq::query()->where('question->ar', $questionAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Faq::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'faqs';
    }
}

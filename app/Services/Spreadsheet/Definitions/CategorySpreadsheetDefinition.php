<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Category;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class CategorySpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Parent Name (AR)', 'Name (EN)', 'Name (AR)',
            'Short Text (EN)', 'Short Text (AR)',
            'Text (EN)', 'Text (AR)',
            'Slug (EN)', 'Slug (AR)',
            'Meta Title (EN)', 'Meta Title (AR)',
            'Meta Desc (EN)', 'Meta Desc (AR)',
            'Status (1=Active)', 'Order',
            'Show In Navbar (1=Yes)', 'Show In Footer (1=Yes)', 'Main Section (1=Yes)',
        ];
    }

    public function exampleRow(): array
    {
        return [
            '', 'Category', 'تصنيف',
            'Short text', 'نص قصير',
            'Full text', 'نص كامل',
            'category', 'تصنيف',
            'Meta title', 'عنوان ميتا',
            'Meta description', 'وصف ميتا',
            '1', '1', '1', '0', '0',
        ];
    }

    public function exportRows(): array
    {
        return Category::query()->with('parent')->orderBy('order')->get()->map(fn (Category $category) => [
            $category->parent?->getTranslation('name', 'ar') ?? '',
            $category->getTranslation('name', 'en'),
            $category->getTranslation('name', 'ar'),
            $category->getTranslation('short_text', 'en'),
            $category->getTranslation('short_text', 'ar'),
            $category->getTranslation('text', 'en'),
            $category->getTranslation('text', 'ar'),
            $category->getTranslation('slug', 'en'),
            $category->getTranslation('slug', 'ar'),
            $category->getTranslation('meta_title', 'en'),
            $category->getTranslation('meta_title', 'ar'),
            $category->getTranslation('meta_desc', 'en'),
            $category->getTranslation('meta_desc', 'ar'),
            $category->is_active ? '1' : '0',
            $category->order,
            $category->show_in_navbar ? '1' : '0',
            $category->show_in_footer ? '1' : '0',
            $category->is_main_section ? '1' : '0',
        ])->all();
    }

    public function importRow(array $row, int $rowNumber): array
    {
        $nameAr = trim((string) ($row[2] ?? ''));
        if ($nameAr === '') {
            return ['inserted' => 0, 'updated' => 0, 'errors' => [__('admin_trans.import_row_missing_title_ar', ['row' => $rowNumber])]];
        }

        $parentId = null;
        $parentName = trim((string) ($row[0] ?? ''));
        if ($parentName !== '') {
            $parent = Category::query()->where('name->ar', $parentName)->first();
            $parentId = $parent?->id;
        }

        $payload = [
            'parent_id' => $parentId,
            'name' => ['en' => trim((string) ($row[1] ?? '')), 'ar' => $nameAr],
            'short_text' => ['en' => trim((string) ($row[3] ?? '')), 'ar' => trim((string) ($row[4] ?? ''))],
            'text' => ['en' => trim((string) ($row[5] ?? '')), 'ar' => trim((string) ($row[6] ?? ''))],
            'slug' => ['en' => trim((string) ($row[7] ?? '')), 'ar' => trim((string) ($row[8] ?? ''))],
            'meta_title' => ['en' => trim((string) ($row[9] ?? '')), 'ar' => trim((string) ($row[10] ?? ''))],
            'meta_desc' => ['en' => trim((string) ($row[11] ?? '')), 'ar' => trim((string) ($row[12] ?? ''))],
            'is_active' => in_array(trim((string) ($row[13] ?? '1')), ['1', 'true', 'yes'], true),
            'order' => (int) ($row[14] ?? 0),
            'show_in_navbar' => in_array(trim((string) ($row[15] ?? '0')), ['1', 'true', 'yes'], true),
            'show_in_footer' => in_array(trim((string) ($row[16] ?? '0')), ['1', 'true', 'yes'], true),
            'is_main_section' => in_array(trim((string) ($row[17] ?? '0')), ['1', 'true', 'yes'], true),
        ];

        $existing = Category::query()->where('name->ar', $nameAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Category::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'categories';
    }
}

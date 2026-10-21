<?php

namespace App\Services\Spreadsheet\Definitions;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Services\Spreadsheet\Contracts\SpreadsheetDefinition;

class BlogSpreadsheetDefinition implements SpreadsheetDefinition
{
    public function headers(): array
    {
        return [
            'Category (AR)', 'Title (EN)', 'Title (AR)',
            'Excerpt (EN)', 'Excerpt (AR)',
            'Content (EN)', 'Content (AR)',
            'Slug (EN)', 'Slug (AR)',
            'Meta Title (EN)', 'Meta Title (AR)',
            'Meta Desc (EN)', 'Meta Desc (AR)',
            'Published At (Y-m-d H:i)', 'Status (1=Active)', 'Order',
        ];
    }

    public function exampleRow(): array
    {
        return [
            'عام', 'Blog title', 'عنوان المقال',
            'Short excerpt', 'مقتطف قصير',
            'Full content', 'المحتوى الكامل',
            'blog-title', 'عنوان-المقال',
            'Meta title', 'عنوان ميتا',
            'Meta description', 'وصف ميتا',
            now()->format('Y-m-d H:i'), '1', '1',
        ];
    }

    public function exportRows(): array
    {
        return Blog::query()->with('blogCategory')->orderBy('order')->get()->map(fn (Blog $blog) => [
            $blog->blogCategory?->getTranslation('name', 'ar') ?? '',
            $blog->getTranslation('title', 'en'),
            $blog->getTranslation('title', 'ar'),
            $blog->getTranslation('excerpt', 'en'),
            $blog->getTranslation('excerpt', 'ar'),
            $blog->getTranslation('content', 'en'),
            $blog->getTranslation('content', 'ar'),
            $blog->getTranslation('slug', 'en'),
            $blog->getTranslation('slug', 'ar'),
            $blog->getTranslation('meta_title', 'en'),
            $blog->getTranslation('meta_title', 'ar'),
            $blog->getTranslation('meta_desc', 'en'),
            $blog->getTranslation('meta_desc', 'ar'),
            $blog->published_at?->format('Y-m-d H:i'),
            $blog->is_active ? '1' : '0',
            $blog->order,
        ])->all();
    }

    public function importRow(array $row, int $rowNumber): array
    {
        $titleAr = trim((string) ($row[2] ?? ''));
        if ($titleAr === '') {
            return ['inserted' => 0, 'updated' => 0, 'errors' => [__('admin_trans.import_row_missing_title_ar', ['row' => $rowNumber])]];
        }

        $categoryId = null;
        $categoryName = trim((string) ($row[0] ?? ''));
        if ($categoryName !== '') {
            $category = BlogCategory::query()->where('name->ar', $categoryName)->first();
            $categoryId = $category?->id;
        }

        $publishedAt = trim((string) ($row[13] ?? ''));
        $payload = [
            'blog_category_id' => $categoryId,
            'title' => ['en' => trim((string) ($row[1] ?? '')), 'ar' => $titleAr],
            'excerpt' => ['en' => trim((string) ($row[3] ?? '')), 'ar' => trim((string) ($row[4] ?? ''))],
            'content' => ['en' => trim((string) ($row[5] ?? '')), 'ar' => trim((string) ($row[6] ?? ''))],
            'slug' => ['en' => trim((string) ($row[7] ?? '')), 'ar' => trim((string) ($row[8] ?? ''))],
            'meta_title' => ['en' => trim((string) ($row[9] ?? '')), 'ar' => trim((string) ($row[10] ?? ''))],
            'meta_desc' => ['en' => trim((string) ($row[11] ?? '')), 'ar' => trim((string) ($row[12] ?? ''))],
            'published_at' => $publishedAt !== '' ? $publishedAt : null,
            'is_active' => in_array(trim((string) ($row[14] ?? '1')), ['1', 'true', 'yes'], true),
            'order' => (int) ($row[15] ?? 0),
        ];

        $existing = Blog::query()->where('title->ar', $titleAr)->first();
        if ($existing) {
            $existing->update($payload);

            return ['inserted' => 0, 'updated' => 1, 'errors' => []];
        }

        Blog::query()->create($payload);

        return ['inserted' => 1, 'updated' => 0, 'errors' => []];
    }

    public function filenamePrefix(): string
    {
        return 'blogs';
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string, string>
     */
    private const BRAND_REPLACEMENTS = [
        'أتلانتس' => 'أطلانتس',
        'اتلانتس' => 'أطلانتس',
        'إتلانتس' => 'أطلانتس',
    ];

    /**
     * @var array<string, string>
     */
    private const STAINLESS_REPLACEMENTS = [
        'استانلس' => 'ستانلس',
        'أستانلس' => 'ستانلس',
        'إستانلس' => 'ستانلس',
        'استلس' => 'ستانلس',
        'Astanls' => 'Stainless',
        'astanls' => 'stainless',
        'Astls' => 'Stainless',
        'astls' => 'stainless',
    ];

    /**
     * Content columns that may contain the Arabic brand name.
     *
     * @var array<string, list<string>>
     */
    private const BRAND_COLUMNS = [
        'sections' => ['title', 'sub_title', 'text', 'button_text'],
        'products' => ['name', 'short_description', 'description', 'meta_title', 'meta_desc'],
        'blogs' => ['title', 'excerpt', 'content', 'meta_title', 'meta_desc'],
        'services' => ['title', 'short_text', 'text', 'meta_title', 'meta_desc'],
        'categories' => ['name', 'short_text', 'text', 'meta_title', 'meta_desc'],
        'projects' => ['title', 'short_text', 'text', 'meta_title', 'meta_desc'],
        'faqs' => ['question', 'answer'],
    ];

    /**
     * Tables whose every `*_ar` column may contain the Arabic brand name.
     *
     * @var list<string>
     */
    private const BRAND_ARABIC_COLUMN_TABLES = ['organizations', 'site_settings', 'seo_assistants'];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach (self::BRAND_ARABIC_COLUMN_TABLES as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $arabicColumns = array_values(array_filter(
                Schema::getColumnListing($table),
                fn (string $column): bool => str_ends_with($column, '_ar'),
            ));

            $this->replaceInTable($table, $arabicColumns, self::BRAND_REPLACEMENTS);
        }

        foreach (self::BRAND_COLUMNS as $table => $columns) {
            $this->replaceInTable($table, $columns, self::BRAND_REPLACEMENTS);
        }

        $this->replaceInTable(
            'products',
            ['name', 'short_description', 'description', 'meta_title', 'meta_desc', 'slug'],
            self::STAINLESS_REPLACEMENTS,
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }

    /**
     * @param  list<string>  $columns
     * @param  array<string, string>  $replacements
     */
    private function replaceInTable(string $table, array $columns, array $replacements): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $columns = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));

        if ($columns === []) {
            return;
        }

        DB::table($table)
            ->select(['id', ...$columns])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($table, $columns, $replacements): void {
                foreach ($rows as $row) {
                    $changes = [];

                    foreach ($columns as $column) {
                        $original = $row->{$column};

                        if (! is_string($original) || $original === '') {
                            continue;
                        }

                        $updated = $this->replaceInValue($original, $replacements);

                        if ($updated !== $original) {
                            $changes[$column] = $updated;
                        }
                    }

                    if ($changes !== []) {
                        DB::table($table)->where('id', $row->id)->update($changes);
                    }
                }
            });
    }

    /**
     * Replace inside plain strings and inside JSON translations (escaped or not).
     *
     * @param  array<string, string>  $replacements
     */
    private function replaceInValue(string $value, array $replacements): string
    {
        $decoded = json_decode($value, true);

        if (is_array($decoded)) {
            array_walk_recursive($decoded, function (mixed &$item) use ($replacements): void {
                if (is_string($item)) {
                    $item = strtr($item, $replacements);
                }
            });

            $encoded = json_encode($decoded, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            return $encoded === json_encode(json_decode($value, true), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                ? $value
                : $encoded;
        }

        return strtr($value, $replacements);
    }
};

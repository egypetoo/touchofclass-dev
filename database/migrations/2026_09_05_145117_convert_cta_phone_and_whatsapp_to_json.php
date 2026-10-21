<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $rows = DB::table('site_settings')->select('id', 'cta_phone', 'cta_whatsapp')->get();

        foreach ($rows as $row) {
            DB::table('site_settings')->where('id', $row->id)->update([
                'cta_phone' => $this->toJsonList($row->cta_phone),
                'cta_whatsapp' => $this->toJsonList($row->cta_whatsapp),
            ]);
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE site_settings MODIFY cta_phone JSON NULL');
            DB::statement('ALTER TABLE site_settings MODIFY cta_whatsapp JSON NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE site_settings ALTER COLUMN cta_phone TYPE JSON USING cta_phone::json');
            DB::statement('ALTER TABLE site_settings ALTER COLUMN cta_whatsapp TYPE JSON USING cta_whatsapp::json');
        }
        // sqlite keeps text; Laravel array cast still works with JSON strings
    }

    public function down(): void
    {
        $rows = DB::table('site_settings')->select('id', 'cta_phone', 'cta_whatsapp')->get();

        foreach ($rows as $row) {
            DB::table('site_settings')->where('id', $row->id)->update([
                'cta_phone' => $this->firstFromJsonList($row->cta_phone),
                'cta_whatsapp' => $this->firstFromJsonList($row->cta_whatsapp),
            ]);
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE site_settings MODIFY cta_phone VARCHAR(255) NULL');
            DB::statement('ALTER TABLE site_settings MODIFY cta_whatsapp VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE site_settings ALTER COLUMN cta_phone TYPE VARCHAR(255) USING cta_phone::text');
            DB::statement('ALTER TABLE site_settings ALTER COLUMN cta_whatsapp TYPE VARCHAR(255) USING cta_whatsapp::text');
        }
    }

    private function toJsonList(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $trimmed = trim($value);
            if ($trimmed === '') {
                return null;
            }

            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $list = array_values(array_filter(array_map(
                    fn ($item) => is_string($item) ? trim($item) : '',
                    $decoded
                )));

                return $list === [] ? null : json_encode($list);
            }

            return json_encode([$trimmed]);
        }

        return null;
    }

    private function firstFromJsonList(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $first = collect($decoded)
                    ->map(fn ($item) => is_string($item) ? trim($item) : '')
                    ->first(fn ($item) => $item !== '');

                return $first ?: null;
            }

            return $value;
        }

        return null;
    }
};

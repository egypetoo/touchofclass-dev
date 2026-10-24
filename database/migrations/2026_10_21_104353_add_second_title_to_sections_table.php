<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->json('second_title')->nullable()->after('title');
        });

        DB::table('sections')->select('id', 'title')->orderBy('id')->each(function (object $section): void {
            $title = json_decode((string) $section->title, true);

            if (! is_array($title)) {
                return;
            }

            $secondTitle = [];

            foreach ($title as $locale => $value) {
                if (! is_string($value)) {
                    continue;
                }

                $lines = preg_split('/\r\n|\r|\n/u', trim($value)) ?: [];
                $lines = array_values(array_filter(
                    $lines,
                    fn (string $line): bool => trim($line) !== '',
                ));

                if (count($lines) < 2) {
                    continue;
                }

                $title[$locale] = $lines[0];
                $secondTitle[$locale] = $lines[1];
            }

            if ($secondTitle === []) {
                return;
            }

            DB::table('sections')->where('id', $section->id)->update([
                'title' => json_encode($title, JSON_UNESCAPED_UNICODE),
                'second_title' => json_encode($secondTitle, JSON_UNESCAPED_UNICODE),
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sections', function (Blueprint $table) {
            $table->dropColumn('second_title');
        });
    }
};

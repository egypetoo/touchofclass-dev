<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['blogs', 'services', 'products', 'categories'] as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->json('meta_title')->nullable()->after('slug');
                $table->json('meta_desc')->nullable()->after('meta_title');
            });
        }
    }

    public function down(): void
    {
        foreach (['blogs', 'services', 'products', 'categories'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn(['meta_title', 'meta_desc']);
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->json('sitemap_sections')->nullable()->after('google_search_console_verification');
        });
    }

    public function down(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->dropColumn('sitemap_sections');
        });
    }
};

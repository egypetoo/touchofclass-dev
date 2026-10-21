<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->string('google_search_console_verification')->nullable()->after('google_tag_manager_id');
        });
    }

    public function down(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->dropColumn('google_search_console_verification');
        });
    }
};

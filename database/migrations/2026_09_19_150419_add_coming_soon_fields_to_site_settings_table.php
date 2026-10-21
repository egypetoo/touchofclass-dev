<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('coming_soon_enabled')->default(false)->after('recaptcha_secret_key');
            $table->string('coming_soon_title_en')->nullable()->after('coming_soon_enabled');
            $table->string('coming_soon_title_ar')->nullable()->after('coming_soon_title_en');
            $table->text('coming_soon_message_en')->nullable()->after('coming_soon_title_ar');
            $table->text('coming_soon_message_ar')->nullable()->after('coming_soon_message_en');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'coming_soon_enabled',
                'coming_soon_title_en',
                'coming_soon_title_ar',
                'coming_soon_message_en',
                'coming_soon_message_ar',
            ]);
        });
    }
};

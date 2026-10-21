<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('recaptcha_enabled')->default(false)->after('notification_emails');
            $table->string('recaptcha_site_key')->nullable()->after('recaptcha_enabled');
            $table->string('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'recaptcha_enabled',
                'recaptcha_site_key',
                'recaptcha_secret_key',
            ]);
        });
    }
};

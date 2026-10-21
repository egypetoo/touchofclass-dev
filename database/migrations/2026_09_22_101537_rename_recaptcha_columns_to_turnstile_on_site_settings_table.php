<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('turnstile_enabled')->default(false)->after('notification_emails');
            $table->string('turnstile_site_key')->nullable()->after('turnstile_enabled');
            $table->string('turnstile_secret_key')->nullable()->after('turnstile_site_key');
        });

        if (Schema::hasColumn('site_settings', 'recaptcha_enabled')) {
            DB::table('site_settings')->update([
                'turnstile_enabled' => DB::raw('recaptcha_enabled'),
                'turnstile_site_key' => DB::raw('recaptcha_site_key'),
                'turnstile_secret_key' => DB::raw('recaptcha_secret_key'),
            ]);

            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'recaptcha_enabled',
                    'recaptcha_site_key',
                    'recaptcha_secret_key',
                ]);
            });
        }
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('recaptcha_enabled')->default(false)->after('notification_emails');
            $table->string('recaptcha_site_key')->nullable()->after('recaptcha_enabled');
            $table->string('recaptcha_secret_key')->nullable()->after('recaptcha_site_key');
        });

        if (Schema::hasColumn('site_settings', 'turnstile_enabled')) {
            DB::table('site_settings')->update([
                'recaptcha_enabled' => DB::raw('turnstile_enabled'),
                'recaptcha_site_key' => DB::raw('turnstile_site_key'),
                'recaptcha_secret_key' => DB::raw('turnstile_secret_key'),
            ]);

            Schema::table('site_settings', function (Blueprint $table) {
                $table->dropColumn([
                    'turnstile_enabled',
                    'turnstile_site_key',
                    'turnstile_secret_key',
                ]);
            });
        }
    }
};

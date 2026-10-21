<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_social_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_setting_id')->constrained('site_settings')->cascadeOnDelete();
            $table->string('platform', 32);
            $table->string('url', 2048);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['site_setting_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_social_links');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pwa_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar')->nullable();
            $table->string('name_en')->nullable();
            $table->string('short_name_ar')->nullable();
            $table->string('short_name_en')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('theme_color')->default('#f59e0b');
            $table->string('background_color')->default('#ffffff');
            $table->string('start_url')->nullable();
            $table->string('scope')->nullable();
            $table->string('orientation')->default('portrait-primary');
            $table->string('display')->default('standalone');
            $table->string('lang')->default('ar');
            $table->string('dir')->default('rtl');
            $table->json('shortcuts')->nullable();
            $table->json('categories')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pwa_settings');
    }
};

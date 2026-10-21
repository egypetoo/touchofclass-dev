<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo_assistants', function (Blueprint $table) {
            $table->id();

            foreach (['home', 'about', 'contact', 'blog', 'service', 'products', 'gallery', 'branches'] as $page) {
                $table->text("{$page}_meta_title_en")->nullable();
                $table->text("{$page}_meta_desc_en")->nullable();
                $table->text("{$page}_meta_keywords_en")->nullable();
                $table->string("{$page}_meta_robots_en")->nullable()->default('index, follow');
                $table->text("{$page}_meta_title_ar")->nullable();
                $table->text("{$page}_meta_desc_ar")->nullable();
                $table->text("{$page}_meta_keywords_ar")->nullable();
                $table->string("{$page}_meta_robots_ar")->nullable()->default('index, follow');
            }

            $table->string('google_analytics_id')->nullable();
            $table->string('google_tag_manager_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_assistants');
    }
};

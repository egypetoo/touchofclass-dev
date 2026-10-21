<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->text('projects_meta_title_en')->nullable()->after('products_meta_robots_ar');
            $table->text('projects_meta_desc_en')->nullable();
            $table->text('projects_meta_keywords_en')->nullable();
            $table->string('projects_meta_robots_en')->nullable()->default('index, follow');
            $table->text('projects_meta_title_ar')->nullable();
            $table->text('projects_meta_desc_ar')->nullable();
            $table->text('projects_meta_keywords_ar')->nullable();
            $table->string('projects_meta_robots_ar')->nullable()->default('index, follow');
        });
    }

    public function down(): void
    {
        Schema::table('seo_assistants', function (Blueprint $table) {
            $table->dropColumn([
                'projects_meta_title_en',
                'projects_meta_desc_en',
                'projects_meta_keywords_en',
                'projects_meta_robots_en',
                'projects_meta_title_ar',
                'projects_meta_desc_ar',
                'projects_meta_keywords_ar',
                'projects_meta_robots_ar',
            ]);
        });
    }
};

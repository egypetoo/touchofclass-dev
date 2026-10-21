<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->json('name')->nullable();
            $table->json('short_text')->nullable();
            $table->json('text')->nullable();
            $table->json('slug');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_navbar')->default(false);
            $table->boolean('show_in_footer')->default(false);
            $table->boolean('is_main_section')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};

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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            $table->json('name');
            $table->json('short_description')->nullable();
            $table->json('description')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('code')->nullable()->unique();
            $table->integer('quantity')->nullable()->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('status')->nullable();
            $table->json('slug');
            $table->unsignedInteger('sort_order')->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

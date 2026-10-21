<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('catalog_sizes')->nullable()->after('sort_order');
            $table->json('catalog_colors')->nullable()->after('catalog_sizes');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['catalog_sizes', 'catalog_colors']);
        });
    }
};

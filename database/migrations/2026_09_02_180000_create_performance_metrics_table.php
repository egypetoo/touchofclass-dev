<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric');
            $table->decimal('value', 12, 4);
            $table->string('rating');
            $table->string('page');
            $table->text('user_agent')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['metric', 'rating']);
            $table->index(['page', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_metrics');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->json('name')->nullable();
            $table->string('type')->nullable();
            $table->json('address')->nullable();
            $table->string('map_url')->nullable();
            $table->longText('map_embed')->nullable();
            $table->string('phone_1_country_code', 16)->nullable();
            $table->string('phone_1', 32)->nullable();
            $table->string('phone_2_country_code', 16)->nullable();
            $table->string('phone_2', 32)->nullable();
            $table->json('working_hours')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};

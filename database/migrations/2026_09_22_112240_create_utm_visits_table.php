<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('utm_visits', function (Blueprint $table) {
            $table->id();
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('path')->nullable()->index();
            $table->string('landing_page', 500)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('session_id', 100)->nullable()->index();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('utm_visits');
    }
};

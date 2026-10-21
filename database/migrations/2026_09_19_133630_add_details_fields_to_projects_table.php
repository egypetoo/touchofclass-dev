<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('completed_year')->nullable()->after('icon');
            $table->json('area')->nullable()->after('completed_year');
            $table->json('duration')->nullable()->after('area');
            $table->json('features')->nullable()->after('duration');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn(['completed_year', 'area', 'duration', 'features']);
        });
    }
};

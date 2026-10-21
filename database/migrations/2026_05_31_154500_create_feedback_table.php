<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->string('name');
            $table->date('date_of_visit')->nullable();
            $table->string('satisfaction');
            $table->unsignedTinyInteger('staff_friendliness');
            $table->unsignedTinyInteger('service_speed');
            $table->unsignedTinyInteger('product_quality');
            $table->unsignedTinyInteger('value_for_money');
            $table->text('hear_about_us')->nullable();
            $table->text('improvements')->nullable();
            $table->text('additional_comments')->nullable();
            $table->string('recommendation');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};

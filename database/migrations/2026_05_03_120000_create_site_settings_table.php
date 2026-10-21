<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('organization_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('site_name_en')->nullable();
            $table->string('site_name_ar')->nullable();
            $table->string('logo_en_path')->nullable();
            $table->string('logo_ar_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('footer_logo_en_path')->nullable();
            $table->string('footer_logo_ar_path')->nullable();
            $table->string('cta_phone')->nullable();
            $table->string('cta_whatsapp')->nullable();
            $table->string('cta_label_en')->nullable();
            $table->string('cta_label_ar')->nullable();
            $table->string('support_email')->nullable();
            $table->string('footer_copyright_en')->nullable();
            $table->string('footer_copyright_ar')->nullable();
            $table->text('footer_body_en')->nullable();
            $table->text('footer_body_ar')->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};

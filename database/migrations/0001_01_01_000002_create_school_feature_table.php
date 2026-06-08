<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_feature', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('feature_name'); // pelanggaran_siswa, n8n_whatsapp, dashboard_sakit, ekspor_excel
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'feature_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_feature');
    }
};

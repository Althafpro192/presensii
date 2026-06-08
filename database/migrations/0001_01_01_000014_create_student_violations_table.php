<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->constrained('users')->cascadeOnDelete();
            $table->string('violation_type');
            $table->text('description')->nullable();
            $table->date('violation_date');
            $table->string('sanction')->nullable(); // peringatan, skors, dikembalikan_ke_ortu
            $table->timestamps();

            $table->index(['school_id', 'student_id']);
            $table->index('violation_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_violations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // e.g., "X IPA 1", "XI IPS 2"
            $table->integer('grade_level'); // 10, 11, 12
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['school_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};

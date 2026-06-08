<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('classroom_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nis')->unique();
            $table->string('name');
            $table->string('rfid')->unique()->nullable(); // Primary RFID UID
            $table->string('parent_phone')->nullable();
            $table->string('promotion_status')->default('pending');
            $table->timestamp('graduated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['school_id', 'classroom_id']);
            $table->index('rfid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

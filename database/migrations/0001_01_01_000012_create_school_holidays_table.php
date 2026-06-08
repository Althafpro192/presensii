<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_holidays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->date('event_date');
            $table->string('type'); // libur, masuk
            $table->string('name')->nullable();
            $table->boolean('override_government')->default(false);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['school_id', 'event_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_holidays');
    }
};

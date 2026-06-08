<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained();
            $table->string('nis', 20)->unique();
            $table->string('name');
            $table->string('rfid', 50)->unique()->nullable();
            $table->string('parent_phone')->nullable();
            $table->enum('promotion_status', ['pending', 'naik', 'tidak_naik', 'lulus'])->default('pending');
            $table->timestamp('graduated_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('students');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rfid_card_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('rfid_device_id')->nullable()->constrained();
            $table->string('card_uid')->unique();
            $table->enum('status', ['active', 'lost', 'damaged', 'inactive'])->default('active');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('lost_reported_at')->nullable();
            $table->text('lost_note')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rfid_card_assignments');
    }
};
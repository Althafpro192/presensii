<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('address')->nullable();
            $table->string('primary_color', 7)->default('#4361ee');
            $table->string('secondary_color', 7)->default('#3f37c9');
            $table->string('api_key', 32)->unique()->nullable();
            $table->json('settings')->nullable(); // late_threshold, absent_threshold, etc.
            $table->timestamp('subscription_ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};

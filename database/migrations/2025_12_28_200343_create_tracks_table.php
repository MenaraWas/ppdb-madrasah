<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wave_id')->constrained()->cascadeOnDelete();

            $table->string('code'); // PRESTASI / TAHFIDZ / REGULER
            $table->string('name'); // label UI

            $table->boolean('requires_interview')->default(true);

            // kuota & cadangan configurable
            $table->unsignedInteger('quota')->nullable();
            $table->boolean('reserve_enabled')->default(true);
            $table->unsignedInteger('reserve_count')->default(0);

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['wave_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracks');
    }
};

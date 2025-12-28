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
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();

            $table->string('nisn', 20);
            $table->string('name');
            $table->string('whatsapp', 30);

            // waktu inisiasi pertama (untuk leaderboard urutan bawah)
            $table->timestamp('initiated_at')->nullable();
            $table->timestamp('last_activity_at')->nullable();

            // anti-spam ringan (opsional)
            $table->string('initiated_ip', 64)->nullable();
            $table->timestamps();

            $table->unique(['academic_year_id', 'nisn']);
            $table->index('nisn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};

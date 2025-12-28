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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('student_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('wave_id')->constrained()->cascadeOnDelete();
            $table->foreignId('track_id')->constrained()->cascadeOnDelete();

            // status utama (enum string)
            $table->string('status_utama')->default('INISIASI');

            // sub-status (sementara string dulu, nanti bisa enum)
            $table->string('verification_status')->nullable();   // BELUM_DIVERIFIKASI / PERLU_PERBAIKAN / TERVERIFIKASI / DITOLAK
            $table->string('physical_status')->nullable();       // BELUM_DITERIMA / SUDAH_DITERIMA
            $table->string('physical_file_status')->nullable();  // DITAHAN / DICABUT
            $table->string('interview_status')->nullable();      // WAJIB / TERJADWAL / SELESAI / TIDAK_HADIR ...
            $table->string('ranking_status')->nullable();        // BELUM / SUDAH
            $table->string('final_status')->nullable();          // LULUS / TIDAK_LULUS / CADANGAN
            $table->string('enrollment_status')->nullable();     // MENUNGGU / SELESAI / KEDALUWARSA

            // waktu-waktu penting
            $table->timestamp('registered_at')->nullable(); // = initiated_at aplikasi (untuk leaderboard)
            $table->timestamp('submitted_at')->nullable();

            /**
             * TOKEN STATUS (tanpa akun)
             * Simpan hash token di DB (bukan token mentah).
             */
            $table->string('status_token_hash', 64)->unique()->nullable(); // sha256 hex
            $table->timestamp('token_created_at')->nullable();
            $table->timestamp('token_last_sent_at')->nullable();

            $table->timestamps();

            // 1 profile hanya boleh 1 pendaftaran per gelombang
            $table->unique(['student_profile_id', 'wave_id']);
            $table->index(['wave_id', 'track_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

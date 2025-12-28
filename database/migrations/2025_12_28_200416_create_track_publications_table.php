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
        Schema::create('track_publications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('track_id')->constrained()->cascadeOnDelete();

            // BELUM_DIPUBLISH / DIPUBLISH_SEMENTARA / DIKUNCI_FINAL
            $table->string('state')->default('BELUM_DIPUBLISH');

            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();

            // penting untuk Opsi 1: pernah publish => scoring terkunci selamanya
            $table->timestamp('ever_published_at')->nullable();

            $table->timestamp('locked_final_at')->nullable();
            $table->foreignId('locked_by_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique('track_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('track_publications');
    }
};

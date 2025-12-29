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
        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->cascadeOnDelete();

            $table->string('doc_type'); // KK, AKTA, RAPOR, SERTIFIKAT, dll

            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('file_size_bytes')->default(0);
            $table->string('checksum_sha256', 64)->nullable();

            // status upload drive
            $table->enum('upload_status', [
                'TEMP_TERSIMPAN',
                'MENUNGGU_UPLOAD_DRIVE',
                'UPLOAD_DRIVE_PROSES',
                'TERSIMPAN_DI_DRIVE',
                'GAGAL_UPLOAD_DRIVE',
            ])->default('TEMP_TERSIMPAN');

            $table->string('temp_path')->nullable(); // storage/app/...
            $table->string('drive_folder_id')->nullable();
            $table->string('drive_file_id')->nullable();
            $table->string('drive_file_name')->nullable();
            $table->string('drive_web_view_link')->nullable(); // jangan expose publik

            $table->timestamp('uploaded_to_drive_at')->nullable();
            $table->unsignedInteger('retry_count')->default(0);
            $table->text('error_message')->nullable();

            $table->timestamps();

            $table->unique(['application_id', 'doc_type']);
            $table->index(['application_id', 'upload_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('application_documents');
    }
};

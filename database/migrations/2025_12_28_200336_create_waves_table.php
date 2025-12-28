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
        Schema::create('waves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('name');       // "Gelombang 1"
            $table->unsignedInteger('sequence'); // 1,2,3...
            $table->enum('status', ['DRAFT', 'DIBUKA', 'DITUTUP'])->default('DRAFT');
            $table->timestamp('opens_at')->nullable();
            $table->timestamp('closes_at')->nullable();

            $table->timestamps();

            $table->unique(['academic_year_id', 'sequence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waves');
    }
};

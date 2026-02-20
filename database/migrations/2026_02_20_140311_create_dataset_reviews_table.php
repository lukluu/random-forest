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
        Schema::create('dataset_reviews', function (Blueprint $table) {
            $table->id();

            // --- 1. DATA DARI CSV (YANG PENTING SAJA) ---
            $table->string('reviewer_name')->nullable(); // Nama pengulas
            $table->text('review_text');                 // Teks ulasan asli (Wajib untuk AI)
            $table->integer('stars')->nullable();        // Rating bintang 1-5 (Penting untuk acuan label manual)
            $table->timestamp('published_at')->nullable();  // Waktu ulasan (Misal: "2 minggu lalu")

            // --- 2. DATA HASIL ANALISIS SISTEM ---
            $table->string('ground_truth')->nullable();  // Kunci jawaban (Positif/Netral/Negatif)
            $table->string('ai_sentiment')->nullable();  // Hasil tebakan AI (Positif/Netral/Negatif)
            $table->decimal('confidence_score', 5, 2)->nullable(); // Skor keyakinan AI (Contoh: 98.50)

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dataset_reviews');
    }
};

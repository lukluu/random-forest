<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('user_surveys', function (Blueprint $table) {
            $table->id();

            // 1. Data Diri Pengunjung
            $table->string('name');
            $table->string('email'); // Tidak unique, karena user bisa isi survey berkali-kali

            // 2. Data Skor (Skala 1-5) - Dipisah agar bisa dirata-rata
            $table->integer('score_rasa');
            $table->integer('score_harga');
            $table->integer('score_pelayanan');
            $table->integer('score_kebersihan');
            $table->integer('score_keramahan');

            // 3. Kolom Bantuan (Untuk mempercepat query Dashboard)
            $table->decimal('score_average', 3, 2); // Contoh: 4.85

            // 4. Data Review & Hasil AI
            $table->text('review')->nullable(); // Bisa kosong jika user tidak isi
            $table->enum('sentiment', ['Positif', 'Netral', 'Negatif'])->nullable(); // Hasil dari Flask/Random Forest
            $table->string('confidence_score')->nullable(); // Hasil dari Flask/Random Forest
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_surveys');
    }
};

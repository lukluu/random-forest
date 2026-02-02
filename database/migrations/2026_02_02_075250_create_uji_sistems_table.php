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
        Schema::create('uji_sistems', function (Blueprint $table) {
            $table->id();
            // Relasi ke Admin yang melakukan tes
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // 2. Data Skor (Skala 1-5)
            $table->integer('score_rasa');
            $table->integer('score_harga');
            $table->integer('score_pelayanan');
            $table->integer('score_kebersihan');
            $table->integer('score_keramahan');

            // 3. Kolom Bantuan
            $table->decimal('score_average', 3, 2);

            // 4. Data Review & Hasil AI
            $table->text('review')->nullable(); // Input teks ulasan
            $table->enum('sentiment', ['Positif', 'Netral', 'Negatif'])->nullable();
            $table->string('confidence_score')->nullable();

            // Tambahan: Simpan detail probabilitas untuk grafik (Opsional tapi disarankan)
            $table->json('probabilities')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uji_sistems');
    }
};

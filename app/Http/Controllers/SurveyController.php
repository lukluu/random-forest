<?php

namespace App\Http\Controllers;

use App\Models\UserSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Survey; // Pastikan menggunakan Model Survey yang benar
use Illuminate\Support\Facades\Http; // <--- WAJIB: Untuk request ke API Flask

class SurveyController extends Controller
{
    /**
     * Menampilkan halaman input data diri
     */
    public function start()
    {
        return view('survey.user-data');
    }

    /**
     * Memproses data nama & email ke session
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        session(['survey_guest' => $validated]);

        return redirect()->route('survey.questions');
    }

    /**
     * Menampilkan halaman pertanyaan
     */
    public function questions()
    {
        if (!session('survey_guest')) {
            return redirect()->route('survey.start');
        }

        return view('survey.question');
    }

    /**
     * Menyimpan hasil & Menghubungi API FLASK
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $data = $request->validate([
            'rasa'       => 'required|integer|min:1|max:5',
            'harga'      => 'required|integer|min:1|max:5',
            'pelayanan'  => 'required|integer|min:1|max:5',
            'kebersihan' => 'required|integer|min:1|max:5',
            'keramahan'  => 'required|integer|min:1|max:5',
            'review'     => 'nullable|string|max:500',
        ]);

        // 2. Ambil data nama/email dari session
        $guest = session('survey_guest');

        // 3. Hitung Rata-rata Skor
        $totalScore = $data['rasa'] + $data['harga'] + $data['pelayanan'] + $data['kebersihan'] + $data['keramahan'];
        $avgScore   = $totalScore / 5;

        // 4. Default Sentimen & Confidence (Cadangan jika AI mati/review kosong)
        $sentiment = 'Netral';
        $confidenceScore = 0; // Default 0%

        // Logika fallback manual
        if ($avgScore == 3) $sentiment = 'Netral';
        elseif ($avgScore > 3) $sentiment = 'Positif';
        elseif ($avgScore < 3) $sentiment = 'Negatif';

        // ==========================================================
        // 5. INTEGRASI API FLASK (AI PREDICTION)
        // ==========================================================
        if ($request->filled('review')) {
            try {
                // Tembak ke API Flask
                $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                    'ulasan' => $request->review
                ]);

                if ($response->successful()) {
                    $result = $response->json();

                    // A. Ambil Sentimen
                    if (isset($result['sentimen'])) {
                        // Ubah jadi kapital (positif -> Positif)
                        $sentiment = ucfirst($result['sentimen']);
                    }

                    // B. Ambil Confidence Score (Probabilitas Tertinggi)
                    if (isset($result['probabilitas']) && is_array($result['probabilitas'])) {
                        // Cari nilai tertinggi dari array probabilitas (misal: 0.95)
                        $maxProb = max($result['probabilitas']);
                        // Konversi ke persentase (0-100)
                        $confidenceScore = $maxProb * 100;
                    }
                }
            } catch (\Exception $e) {
                // Jika error koneksi ke Flask, biarkan pakai nilai default manual
                Log::error("Flask API Error: " . $e->getMessage());
            }
        }

        // 6. SIMPAN KE DATABASE
        // Pastikan model UserSurvey sudah punya kolom 'confidence_score' di database
        $survey = UserSurvey::create([
            'name'             => $guest['name'],
            'email'            => $guest['email'],
            'score_rasa'       => $data['rasa'],
            'score_harga'      => $data['harga'],
            'score_pelayanan'  => $data['pelayanan'],
            'score_kebersihan' => $data['kebersihan'],
            'score_keramahan'  => $data['keramahan'],
            'score_average'    => $avgScore,
            'review'           => $request->review,
            'sentiment'        => $sentiment,
            'confidence_score' => $confidenceScore, // <--- MENYIMPAN SKOR KEPERCAYAAN
        ]);

        // Simpan ID survey terakhir ke session untuk ditampilkan di halaman result
        session(['last_survey_id' => $survey->id]);

        return redirect()->route('survey.result');
    }
    /**
     * Menampilkan Hasil
     */
    public function result()
    {
        $lastSurveyId = session('last_survey_id');

        if (!$lastSurveyId) {
            return redirect()->route('survey.start');
        }

        $currentResult = UserSurvey::find($lastSurveyId);

        // Ambil 5 riwayat terakhir selain yang baru saja diinput
        $history = UserSurvey::where('id', '!=', $lastSurveyId)
            ->latest()
            ->take(5)
            ->get();

        return view('survey.result', compact('currentResult', 'history'));
    }
}

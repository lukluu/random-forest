<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UjiSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SystemTestController extends Controller
{
    /**
     * Menampilkan halaman form uji sistem manual.
     */
    public function index()
    {
        return view('admin.uji-sistem.index');
    }

    /**
     * Memproses input manual, kirim ke Flask, dan simpan ke DB.
     */
    public function process(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'ulasan_manual' => 'required|string|max:1000',
            // Validasi Slider (Pastikan name di view sesuai: rasa, harga, dll)
            'rasa'       => 'required|integer|min:1|max:5',
            'harga'      => 'required|integer|min:1|max:5',
            'pelayanan'  => 'required|integer|min:1|max:5',
            'kebersihan' => 'required|integer|min:1|max:5',
            'keramahan'  => 'required|integer|min:1|max:5',
        ]);

        $ulasan = $request->ulasan_manual;

        // 2. Hitung Rata-rata Skor (Opsional, untuk kelengkapan data)
        $totalScore = $request->rasa + $request->harga + $request->pelayanan + $request->kebersihan + $request->keramahan;
        $avgScore   = $totalScore / 5;

        // 3. Siapkan Variabel Hasil Default
        $result = null;

        // ==========================================================
        // 4. INTEGRASI API FLASK (AI PREDICTION)
        // ==========================================================
        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                'ulasan' => $ulasan
            ]);

            if ($response->successful()) {
                $json = $response->json();

                // Siapkan data untuk View & DB
                $result = [
                    'sentiment'  => ucfirst($json['sentimen'] ?? 'Error'),
                    'confidence' => 0,
                    'probs'      => $json['probabilitas'] ?? []
                ];

                // Ambil confidence score tertinggi
                if (isset($json['probabilitas'])) {
                    $result['confidence'] = max($json['probabilitas']) * 100;
                }

                // ==========================================================
                // 5. SIMPAN KE DATABASE (Tabel uji_sistem)
                // ==========================================================
                UjiSistem::create([
                    'user_id'          => Auth::id(), // ID Admin yang sedang login

                    // Data Skor
                    'score_rasa'       => $request->rasa,
                    'score_harga'      => $request->harga,
                    'score_pelayanan'  => $request->pelayanan,
                    'score_kebersihan' => $request->kebersihan,
                    'score_keramahan'  => $request->keramahan,
                    'score_average'    => $avgScore,

                    // Data Text & Hasil AI
                    'review'           => $ulasan, // Masuk ke kolom 'review' sesuai migration baru
                    'sentiment'        => $result['sentiment'],
                    'confidence_score' => (string) $result['confidence'], // Simpan sebagai string/float
                    'probabilities'    => $result['probs'], // Disimpan sebagai JSON
                ]);
            } else {
                return back()->with('error', 'Gagal mendapatkan respon dari AI Server.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan koneksi ke AI: ' . $e->getMessage());
        }

        // 6. Kembalikan ke View dengan Hasil
        return view('admin.uji-sistem.index', compact('result', 'ulasan'));
    }

    /**
     * Menampilkan riwayat pengujian.
     */
    public function riwayat()
    {
        // Ambil data terbaru dengan pagination
        $riwayat = UjiSistem::with('user')->latest()->paginate(10);

        // Pastikan view yang dipanggil sesuai nama file Anda
        return view('admin.uji-sistem.riwayat-uji', compact('riwayat'));
    }
}

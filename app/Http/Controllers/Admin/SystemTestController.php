<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UjiSistem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class SystemTestController extends Controller
{
    public function index()
    {
        return view('admin.uji-sistem.index');
    }

    /**
     * Memproses input manual, kirim ke Flask, dan simpan ke DB.
     */
    public function process(Request $request)
    {
        // 1. Validasi Input (Tambahkan ground_truth)
        $request->validate([
            'ulasan_manual' => 'required|string|max:1000',
            'ground_truth'  => 'required|in:Positif,Netral,Negatif', // <--- INI PENTING
            'rasa'          => 'required|integer|min:1|max:5',
            'harga'         => 'required|integer|min:1|max:5',
            'pelayanan'     => 'required|integer|min:1|max:5',
            'kebersihan'    => 'required|integer|min:1|max:5',
            'keramahan'     => 'required|integer|min:1|max:5',
        ]);

        $ulasan = $request->ulasan_manual;
        $groundTruthInput = $request->ground_truth; // Ambil dari input manual

        // 2. Hitung Rata-rata Skor (Hanya untuk data pelengkap)
        $totalScore = $request->rasa + $request->harga + $request->pelayanan + $request->kebersihan + $request->keramahan;
        $avgScore   = $totalScore / 5;

        // 3. Integrasi API Flask
        try {
            $response = Http::timeout(5)->post('http://127.0.0.1:5000/predict', [
                'ulasan' => $ulasan
            ]);

            if ($response->successful()) {
                $json = $response->json();

                $result = [
                    'sentiment'  => ucfirst($json['sentimen'] ?? 'Error'),
                    'confidence' => 0,
                    'probs'      => $json['probabilitas'] ?? []
                ];

                if (isset($json['probabilitas'])) {
                    $result['confidence'] = max($json['probabilitas']) * 100;
                }

                // 4. Cek Konsistensi (Match vs Mismatch)
                $analisisKonsistensi = ($result['sentiment'] == $groundTruthInput) ? 'Cocok' : 'Tidak Cocok';

                // 5. Simpan ke Database
                UjiSistem::create([
                    'user_id'          => Auth::id(),
                    'score_rasa'       => $request->rasa,
                    'score_harga'      => $request->harga,
                    'score_pelayanan'  => $request->pelayanan,
                    'score_kebersihan' => $request->kebersihan,
                    'score_keramahan'  => $request->keramahan,
                    'score_average'    => $avgScore,
                    'ground_truth'     => $groundTruthInput, // Simpan input manual user

                    'review'           => $ulasan,
                    'sentiment'        => $result['sentiment'],
                    'confidence_score' => (string) $result['confidence'],
                    'probabilities'    => $result['probs'],
                ]);

                return view('admin.uji-sistem.index', compact(
                    'result',
                    'ulasan',
                    'avgScore',
                    'analisisKonsistensi',
                    'groundTruthInput' // Kirim balik input user untuk ditampilkan di alert
                ));
            } else {
                return back()->with('error', 'Gagal mendapatkan respon dari AI Server.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan koneksi ke AI: ' . $e->getMessage());
        }
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
    public function analisis()
    {
        $dataset = UjiSistem::all();
        $totalData = $dataset->count();
        $correctCount = 0;
        $mismatchCount = 0;

        $matrix = [
            'Positif' => ['Positif' => 0, 'Netral' => 0, 'Negatif' => 0],
            'Netral'  => ['Positif' => 0, 'Netral' => 0, 'Negatif' => 0],
            'Negatif' => ['Positif' => 0, 'Netral' => 0, 'Negatif' => 0],
        ];

        foreach ($dataset as $data) {
            // Gunakan Ground Truth dari Database (Input Manual User)
            $manualLabel = $data->ground_truth ?? 'Netral';

            if ($manualLabel === $data->sentiment) {
                $correctCount++;
            } else {
                $mismatchCount++;
            }

            if (isset($matrix[$manualLabel][$data->sentiment])) {
                $matrix[$manualLabel][$data->sentiment]++;
            }
        }

        $accuracy = $totalData > 0 ? ($correctCount / $totalData) * 100 : 0;

        $mismatches = $dataset->filter(function ($data) {
            return $data->ground_truth !== $data->sentiment;
        });

        $chartData = [
            'series'   => [$correctCount, $mismatchCount],
            'labels'   => ['Prediksi Benar', 'Salah Prediksi'],
            'colors'   => ['#10B981', '#EF4444'],
            'accuracy' => number_format($accuracy, 0) . '%'
        ];

        return view('admin.uji-sistem.analisis', compact('totalData', 'accuracy', 'matrix', 'mismatches', 'chartData'));
    }

    /**
     * Menampilkan Detail Satu Pengujian
     */
    public function show($id)
    {
        $data = UjiSistem::findOrFail($id);
        return view('admin.uji-sistem.riwayat-detail', compact('data'));
    }

    /**
     * Menghapus Satu Data Pengujian
     */
    public function destroy($id)
    {
        try {
            $data = UjiSistem::findOrFail($id);
            $data->delete();
            return back()->with('success', 'Data pengujian berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data.');
        }
    }

    /**
     * Reset (Hapus Semua) Riwayat Pengujian
     */
    public function resetHistory()
    {
        try {
            // Menghapus semua data di tabel uji_sistems
            UjiSistem::truncate();
            return back()->with('success', 'Seluruh riwayat pengujian berhasil di-reset.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mereset data.');
        }
    }

    /**
     * Memproses upload file CSV dataset, meloopingnya ke Flask API
     */
    public function testDataset(Request $request)
    {
        // 1. Waktu eksekusi tidak terbatas (karena memproses seluruh data CSV butuh waktu)
        set_time_limit(0);

        $request->validate([
            'dataset_file' => 'required|file|max:51200' // Max diperbesar jadi 50MB
        ], [
            'dataset_file.required' => 'Anda belum memilih file dataset.',
            'dataset_file.max' => 'Ukuran file tidak boleh lebih dari 50 MB.'
        ]);

        $file = $request->file('dataset_file');
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt'])) {
            return back()->with('error', 'Format file harus .csv. File Anda terdeteksi sebagai .' . $extension);
        }

        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);

        if (!$header) {
            return back()->with('error', 'Gagal membaca isi CSV atau file kosong.');
        }

        $headerLower = array_map(function ($col) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col)));
        }, $header);

        $textIndex = array_search('text', $headerLower);
        if ($textIndex === false) $textIndex = array_search('ulasan', $headerLower);

        $starsIndex = array_search('stars', $headerLower);
        if ($starsIndex === false) $starsIndex = array_search('rating', $headerLower);
        $gtIndex = array_search('ground_truth', $headerLower);

        if ($textIndex === false) {
            fclose($handle);
            return back()->with('error', 'Sistem tidak menemukan kolom dengan header "text" atau "ulasan" di baris pertama CSV.');
        }

        $results = [];
        $stats = ['Positif' => 0, 'Netral' => 0, 'Negatif' => 0];
        $correctCount = 0;
        $totalEvaluated = 0;
        $failedApiCount = 0;
        $count = 0;

        // 2. Looping SELURUH isi CSV (Tanpa Limit)
        while (($row = fgetcsv($handle)) !== false) {
            if (!isset($row[$textIndex]) || empty(trim($row[$textIndex]))) continue;

            $text = $row[$textIndex];

            $groundTruth = '-';
            if ($gtIndex !== false && isset($row[$gtIndex]) && !empty($row[$gtIndex])) {
                $groundTruth = ucfirst(strtolower($row[$gtIndex]));
            } elseif ($starsIndex !== false && isset($row[$starsIndex]) && is_numeric($row[$starsIndex])) {
                $stars = (int)$row[$starsIndex];
                if ($stars >= 4) $groundTruth = 'Positif';
                elseif ($stars <= 2) $groundTruth = 'Negatif';
                else $groundTruth = 'Netral';
            }

            try {
                // Timeout API diatur pendek (3 detik per request) agar jika Flask mati, loop cepat selesai
                $response = Http::timeout(3)->post('http://127.0.0.1:5000/predict', [
                    'ulasan' => $text
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $pred = ucfirst($json['sentimen'] ?? 'Netral');

                    if (isset($stats[$pred])) {
                        $stats[$pred]++;
                    } else {
                        $stats['Netral']++;
                        $pred = 'Netral';
                    }

                    if ($groundTruth !== '-') {
                        $totalEvaluated++;
                        if ($groundTruth === $pred) {
                            $correctCount++;
                        }
                    }

                    $results[] = [
                        'text' => $text,
                        'ground_truth' => $groundTruth,
                        'prediction' => $pred,
                        'confidence' => isset($json['probabilitas']) ? max($json['probabilitas']) * 100 : 0
                    ];
                    $count++;
                } else {
                    $failedApiCount++;
                }
            } catch (\Exception $e) {
                $failedApiCount++;
            }
        }

        fclose($handle);

        if ($count === 0 && $failedApiCount == 0) {
            return back()->with('error', 'Tidak ada data ulasan yang berhasil diproses. Cek kembali format CSV Anda.');
        }

        $accuracy = $totalEvaluated > 0 ? ($correctCount / $totalEvaluated) * 100 : 0;

        return view('admin.uji-sistem.dataset-result', compact(
            'results',
            'stats',
            'accuracy',
            'totalEvaluated',
            'failedApiCount',
            'count'
        ));
    }
}

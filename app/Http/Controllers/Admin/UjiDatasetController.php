<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DatasetReview;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class UjiDatasetController extends Controller
{
    public function index()
    {
        return view('admin.uji-sistem-dataset.index');
    }
    /**
     * Memproses upload file CSV dataset, meloopingnya ke Flask API
     * dan menyimpannya ke database dataset_reviews (Timpa jika ada).
     */
    public function testDataset(Request $request)
    {
        // 1. Waktu eksekusi tidak terbatas
        set_time_limit(0);

        // 2. Validasi File
        $request->validate([
            'dataset_file' => 'required|file|max:51200'
        ], [
            'dataset_file.required' => 'Anda belum memilih file dataset.',
            'dataset_file.max' => 'Ukuran file tidak boleh lebih dari 50 MB.'
        ]);

        $file = $request->file('dataset_file');

        // Cek Ekstensi Manual
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

        // 3. Bersihkan Header & Cari Index Kolom
        $headerLower = array_map(function ($col) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $col)));
        }, $header);

        $textIndex = array_search('text', $headerLower);
        if ($textIndex === false) $textIndex = array_search('ulasan', $headerLower);

        $starsIndex = array_search('stars', $headerLower);
        if ($starsIndex === false) $starsIndex = array_search('rating', $headerLower);

        $nameIndex = array_search('name', $headerLower);
        if ($nameIndex === false) $nameIndex = array_search('reviewer_name', $headerLower);

        $dateIndex = array_search('publishedatdate', $headerLower);
        if ($dateIndex === false) $dateIndex = array_search('published_at_date', $headerLower);

        $gtIndex = array_search('ground_truth', $headerLower);

        if ($textIndex === false) {
            fclose($handle);
            return back()->with('error', 'Sistem tidak menemukan kolom "text" atau "ulasan" di baris pertama CSV.');
        }

        $results = [];
        $stats = ['Positif' => 0, 'Netral' => 0, 'Negatif' => 0];
        $correctCount = 0;
        $totalEvaluated = 0;
        $failedApiCount = 0;
        $count = 0;

        $insertedCount = 0;
        $updatedCount = 0;

        // 4. Looping SELURUH isi CSV
        while (($row = fgetcsv($handle)) !== false) {
            if (!isset($row[$textIndex]) || empty(trim($row[$textIndex]))) continue;

            $text = $row[$textIndex];
            $reviewerName = $nameIndex !== false && isset($row[$nameIndex]) ? $row[$nameIndex] : null;

            // Parsing Waktu dengan Carbon
            $publishedAt = null;
            if ($dateIndex !== false && isset($row[$dateIndex]) && !empty($row[$dateIndex])) {
                try {
                    $publishedAt = Carbon::parse($row[$dateIndex])->timezone('Asia/Makassar')->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    $publishedAt = null;
                }
            }

            // Konversi Stars
            $stars = null;
            if ($starsIndex !== false && isset($row[$starsIndex]) && is_numeric($row[$starsIndex])) {
                $stars = (int)$row[$starsIndex];
            }

            // Tentukan Ground Truth
            $groundTruth = null;
            if ($gtIndex !== false && isset($row[$gtIndex]) && !empty($row[$gtIndex])) {
                $groundTruth = ucfirst(strtolower($row[$gtIndex]));
            } elseif ($stars !== null) {
                if ($stars >= 4) $groundTruth = 'Positif';
                elseif ($stars <= 2) $groundTruth = 'Negatif';
                else $groundTruth = 'Netral';
            }

            // 5. Tembak ke API Flask (Proses tetap jalan untuk semua data)
            try {
                $response = Http::timeout(3)->post('http://127.0.0.1:5000/predict', [
                    'ulasan' => $text
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $pred = ucfirst($json['sentimen'] ?? 'Netral');
                    $confidence = isset($json['probabilitas']) ? max($json['probabilitas']) * 100 : 0;

                    if (isset($stats[$pred])) {
                        $stats[$pred]++;
                    } else {
                        $stats['Netral']++;
                        $pred = 'Netral';
                    }

                    if ($groundTruth !== null) {
                        $totalEvaluated++;
                        if ($groundTruth === $pred) {
                            $correctCount++;
                        }
                    }

                    // =====================================================================
                    // PERBAIKAN: SIMPAN BERDASARKAN KOMBINASI (NAMA + TEKS + WAKTU)
                    // =====================================================================
                    $record = DatasetReview::updateOrCreate(
                        [
                            'reviewer_name' => $reviewerName,
                            'review_text'   => $text,
                            'published_at'  => $publishedAt,
                        ], // Kondisi pencarian (Cari ulasan dengan nama, teks, dan waktu yang sama)
                        [
                            'stars'            => $stars,
                            'ground_truth'     => $groundTruth,
                            'ai_sentiment'     => $pred,
                            'confidence_score' => $confidence,
                        ] // Data yang di-update/insert
                    );

                    // Cek apakah data ini baru dibuat atau hanya di-update
                    if ($record->wasRecentlyCreated) {
                        $insertedCount++;
                    } else {
                        $updatedCount++;
                    }

                    // Data untuk View Hasil
                    $results[] = [
                        'text' => $text,
                        'ground_truth' => $groundTruth ?? '-',
                        'prediction' => $pred,
                        'confidence' => $confidence
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
            return back()->with('error', 'Tidak ada data yang berhasil diproses. Cek format CSV Anda.');
        }

        // Pesan Sukses Dinamis
        $msg = "Selesai! Berhasil memproses $count data.";
        if ($insertedCount > 0 || $updatedCount > 0) {
            $msg .= " ($insertedCount disimpan baru, $updatedCount data lama ditimpa karena duplikat).";
        }
        session()->flash('success', $msg);

        $accuracy = $totalEvaluated > 0 ? ($correctCount / $totalEvaluated) * 100 : 0;

        return view('admin.uji-sistem-dataset.dataset-result', compact(
            'results',
            'stats',
            'accuracy',
            'totalEvaluated',
            'failedApiCount',
            'count'
        ));
    }

    public function riwayat()
    {
        // Ambil data terbaru dari tabel dataset_reviews dengan paginasi
        $riwayat = DatasetReview::latest()->paginate(15);

        $totalData = DatasetReview::count();
        $totalPositif = DatasetReview::where('ai_sentiment', 'Positif')->count();
        $totalNetral = DatasetReview::where('ai_sentiment', 'Netral')->count();
        $totalNegatif = DatasetReview::where('ai_sentiment', 'Negatif')->count();

        return view('admin.uji-sistem-dataset.riwayat', compact(
            'riwayat',
            'totalData',
            'totalPositif',
            'totalNetral',
            'totalNegatif'
        ));
    }

    /**
     * [BARU] Menghapus seluruh data dari tabel dataset_reviews
     */
    public function resetDataset()
    {
        try {
            DatasetReview::truncate();
            return back()->with('success', 'Seluruh data riwayat dataset berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    /**
     * [BARU] Menampilkan halaman Analisis/Statistik Dataset
     */
    public function analisis(Request $request)
    {
        // 1. Tangkap parameter filter dari URL
        $yearFilter = $request->query('year');
        $monthFilter = $request->query('month');
        $range = $request->query('range', 'monthly'); // Default tampil per bulan

        // 2. Query ke Database dengan Filter Waktu
        $query = DatasetReview::query();

        if (!empty($yearFilter)) {
            $query->whereYear('published_at', $yearFilter);
        }
        if (!empty($monthFilter)) {
            $query->whereMonth('published_at', $monthFilter);
        }

        $dataset = $query->get();
        $totalData = $dataset->count();

        // 3. Hitung Statistik Sentimen dari data yang ter-filter
        $totalPositif = $dataset->where('ai_sentiment', 'Positif')->count();
        $totalNetral = $dataset->where('ai_sentiment', 'Netral')->count();
        $totalNegatif = $dataset->where('ai_sentiment', 'Negatif')->count();

        // Hitung Akurasi
        $evaluated = $dataset->filter(function ($item) {
            return !empty($item->ground_truth) && $item->ground_truth !== '-';
        });
        $totalEvaluated = $evaluated->count();
        $correctCount = $evaluated->filter(function ($item) {
            return strtolower($item->ground_truth) === strtolower($item->ai_sentiment);
        })->count();

        $accuracy = $totalEvaluated > 0 ? ($correctCount / $totalEvaluated) * 100 : 0;

        // 4. Grouping Data untuk Grafik Tren
        $reviewsWithDate = $dataset->filter(function ($item) {
            return !empty($item->published_at);
        });

        $grouped = $reviewsWithDate->groupBy(function ($item) use ($range) {
            $date = \Carbon\Carbon::parse($item->published_at);

            if ($range === 'weekly') {
                return $date->format('o-\WW'); // Format Minggu: 2023-W01
            } elseif ($range === 'yearly') {
                return $date->format('Y');     // Format Tahun: 2023
            } else {
                return $date->format('Y-m');   // Format Bulan: 2023-01
            }
        })->sortKeys();

        $chartLabels = [];
        $trendPositif = [];
        $trendNetral = [];
        $trendNegatif = [];

        foreach ($grouped as $key => $items) {
            if ($range === 'weekly') {
                $year = substr($key, 0, 4);
                $week = substr($key, 6);
                $label = "Mg $week, $year";
            } elseif ($range === 'yearly') {
                $label = $key;
            } else {
                $label = \Carbon\Carbon::createFromFormat('Y-m', $key)->translatedFormat('M Y');
            }

            $chartLabels[]  = $label;
            $trendPositif[] = $items->where('ai_sentiment', 'Positif')->count();
            $trendNetral[]  = $items->where('ai_sentiment', 'Netral')->count();
            $trendNegatif[] = $items->where('ai_sentiment', 'Negatif')->count();
        }

        // 5. Ambil daftar tahun yang tersedia di database untuk Dropdown Filter
        $availableYears = DatasetReview::whereNotNull('published_at')
            ->pluck('published_at')
            ->map(function ($date) {
                return \Carbon\Carbon::parse($date)->year;
            })
            ->unique()
            ->sortDesc()
            ->values();

        return view('admin.uji-sistem-dataset.analisis', compact(
            'totalData',
            'totalPositif',
            'totalNetral',
            'totalNegatif',
            'accuracy',
            'totalEvaluated',
            'chartLabels',
            'trendPositif',
            'trendNetral',
            'trendNegatif',
            'range',
            'yearFilter',
            'monthFilter',
            'availableYears'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Models\UserSurvey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. HITUNG STATISTIK UTAMA ($stats)
        // ==========================================
        $totalResponden = UserSurvey::count();

        // Rata-rata Skor Total
        $avgScore = $totalResponden > 0 ? UserSurvey::avg('score_average') : 0;

        // Mencari Sentimen Dominan
        $topSentiment = UserSurvey::select('sentiment', DB::raw('count(*) as total'))
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->orderByDesc('total')
            ->first();

        $sentimentDominantLabel = $topSentiment ? $topSentiment->sentiment : '-';
        $sentimentPercentage = 0;

        if ($topSentiment && $totalResponden > 0) {
            $sentimentPercentage = ($topSentiment->total / $totalResponden) * 100;
        }

        $stats = [
            'total_responden'      => $totalResponden,
            'avg_score'            => number_format($avgScore, 1),
            'sentiment_dominant'   => $sentimentDominantLabel,
            'sentiment_percentage' => round($sentimentPercentage),
        ];

        // ==========================================
        // 2. DATA UNTUK BAR CHART ($chartData)
        // ==========================================
        $averages = UserSurvey::selectRaw('
            AVG(score_rasa) as rasa,
            AVG(score_harga) as harga,
            AVG(score_pelayanan) as pelayanan,
            AVG(score_kebersihan) as kebersihan,
            AVG(score_keramahan) as keramahan
        ')->first();

        $scores = [
            round($averages->rasa ?? 0, 1),
            round($averages->harga ?? 0, 1),
            round($averages->pelayanan ?? 0, 1),
            round($averages->kebersihan ?? 0, 1),
            round($averages->keramahan ?? 0, 1),
        ];

        $chartData = [
            'labels' => ['Rasa', 'Harga', 'Pelayanan', 'Kebersihan', 'Keramahan'],
            'scores' => $scores
        ];

        // ==========================================
        // 3. DATA UNTUK DONUT CHART ($pieChartPercentages) -> INI YANG HILANG TADI
        // ==========================================
        $sentimentCounts = UserSurvey::select('sentiment', DB::raw('count(*) as total'))
            ->whereNotNull('sentiment')
            ->groupBy('sentiment')
            ->pluck('total', 'sentiment')
            ->toArray();

        // Data untuk grafik ApexChart (Urutan: Positif, Netral, Negatif)
        $pieChartData = [
            $sentimentCounts['Positif'] ?? 0,
            $sentimentCounts['Netral'] ?? 0,
            $sentimentCounts['Negatif'] ?? 0
        ];

        // Hitung persentase untuk label HTML di bawah grafik
        $totalSentiment = array_sum($pieChartData);
        $pieChartPercentages = [
            'Positif' => $totalSentiment > 0 ? round(($pieChartData[0] / $totalSentiment) * 100) : 0,
            'Netral'  => $totalSentiment > 0 ? round(($pieChartData[1] / $totalSentiment) * 100) : 0,
            'Negatif' => $totalSentiment > 0 ? round(($pieChartData[2] / $totalSentiment) * 100) : 0,
        ];

        // ==========================================
        // 4. DATA TABEL TERBARU ($recentSurveys)
        // ==========================================
        $recentData = UserSurvey::latest()
            ->take(5)
            ->get();

        $recentSurveys = $recentData->map(function ($item) {
            return [
                'name'      => $item->name,
                'date'      => $item->created_at->translatedFormat('d M Y'),
                'score'     => $item->score_average,
                'sentiment' => $item->sentiment ?? 'Belum Dianalisis',
                'review'    => $item->review,
            ];
        });

        // Kirim SEMUA variabel ke View
        return view('admin.dashboard', compact(
            'stats',
            'chartData',
            'recentSurveys',
            'pieChartData',       // Data Angka untuk Grafik
            'pieChartPercentages' // Data Persentase untuk Teks HTML
        ));
    }
    /**
     * Menampilkan Halaman Statistik Detail
     */
    public function statistics(Request $request)
    {
        // 1. Tentukan Filter (Default: Monthly)
        $filter = $request->query('filter', 'daily');

        // Setup variabel query
        $query = UserSurvey::query();
        $dateFormat = '';
        $groupBy = '';
        $limit = 0;
        $labelFormat = ''; // Format tampilan tanggal di Chart

        // 2. Logika Switch Filter (MySQL Syntax)
        switch ($filter) {
            case 'weekly':
                // 8 Minggu Terakhir
                $query->where('created_at', '>=', now()->subWeeks(8));
                $dateFormat = '%Y-%u';
                $labelFormat = 'W - M Y';
                break;

            case 'monthly':
                // 12 Bulan Terakhir
                $query->where('created_at', '>=', now()->subMonths(12));
                $dateFormat = '%Y-%m';
                $labelFormat = 'M Y';
                break;

            case 'daily':
            default: // <--- INI PENTING: Logic harian masuk ke default
                // 7 Hari Terakhir
                $query->where('created_at', '>=', now()->subDays(7));
                $dateFormat = '%Y-%m-%d';
                $labelFormat = 'd M';
                break;
        }

        // ========================================================
        // A. DATA TREN KEPUASAN (Line Chart)
        // ========================================================
        $trendDataRaw = UserSurvey::select(
            DB::raw("DATE_FORMAT(created_at, '$dateFormat') as date"),
            DB::raw('AVG(score_average) as avg_score')
        )
            ->where('created_at', '>=', match ($filter) {
                'daily' => now()->subDays(7),
                'weekly' => now()->subWeeks(8),
                default => now()->subMonths(12),
            })
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        $trendLabels = [];
        $trendScores = [];

        foreach ($trendDataRaw as $data) {
            // Format label agar cantik
            if ($filter == 'weekly') {
                $trendLabels[] = 'Minggu ke-' . substr($data->date, -2);
            } else {
                $trendLabels[] = \Carbon\Carbon::parse($data->date)->translatedFormat($labelFormat);
            }
            $trendScores[] = round($data->avg_score, 1);
        }

        // ========================================================
        // B. DATA TREN SENTIMEN (Stacked Bar Chart)
        // ========================================================
        // Kita butuh pivot data: Tanggal -> Jumlah Positif, Netral, Negatif
        $sentimentRaw = UserSurvey::select(
            DB::raw("DATE_FORMAT(created_at, '$dateFormat') as date"),
            'sentiment',
            DB::raw('count(*) as total')
        )
            ->whereNotNull('sentiment')
            ->where('created_at', '>=', match ($filter) {
                'daily' => now()->subDays(7),
                'weekly' => now()->subWeeks(8),
                default => now()->subMonths(12),
            })
            ->groupBy('date', 'sentiment')
            ->orderBy('date', 'ASC')
            ->get();

        // Init array agar urutan tanggal sama dengan Trend Chart
        $sentimentData = [
            'Positif' => array_fill(0, count($trendLabels), 0),
            'Netral'  => array_fill(0, count($trendLabels), 0),
            'Negatif' => array_fill(0, count($trendLabels), 0),
        ];

        // Mapping data raw ke struktur array chart
        // Ini agak trik karena kita harus mencocokkan tanggal raw dengan index label
        $dateMap = $trendDataRaw->pluck('date')->toArray(); // ['2026-01', '2026-02']

        foreach ($sentimentRaw as $row) {
            $index = array_search($row->date, $dateMap);
            if ($index !== false && isset($sentimentData[$row->sentiment])) {
                $sentimentData[$row->sentiment][$index] = $row->total;
            }
        }

        // ========================================================
        // C. KATA KUNCI POPULER (Real dari Database)
        // ========================================================
        // Ambil 50 review terakhir, pecah kata, hitung frekuensi
        $reviews = UserSurvey::latest()->take(50)->pluck('review')->toArray();
        $allText = strtolower(implode(' ', $reviews));

        // Hapus tanda baca
        $allText = preg_replace('/[^a-z0-9\s]/', '', $allText);
        $words = explode(' ', $allText);

        // Filter kata umum (Stopwords sederhana)
        $stopwords = ['dan', 'yang', 'di', 'ini', 'itu', 'sangat', 'banget', 'saya', 'aku', 'ke', 'dari', 'tapi', 'agak', 'cukup', 'lumayan', 'nya', 'karena', 'buat', 'sama', 'juga'];
        $words = array_filter($words, function ($w) use ($stopwords) {
            return strlen($w) > 3 && !in_array($w, $stopwords);
        });

        // Hitung frekuensi
        $wordCounts = array_count_values($words);
        arsort($wordCounts);

        // Ambil top 10
        $topKeywords = [];
        $i = 0;
        foreach ($wordCounts as $word => $count) {
            if ($i >= 10) break;

            // Tentukan sentimen kata (Sederhana)
            // Idealnya pakai kamus, tapi ini logic simpel untuk visualisasi
            $sentiment = 'Netral';
            if (in_array($word, ['enak', 'bagus', 'bersih', 'ramah', 'cepat', 'mantap', 'oke', 'suka'])) $sentiment = 'Positif';
            if (in_array($word, ['kotor', 'lama', 'mahal', 'buruk', 'kecewa', 'asin', 'dingin'])) $sentiment = 'Negatif';

            $topKeywords[] = [
                'word' => ucfirst($word),
                'count' => $count,
                'sentiment' => $sentiment
            ];
            $i++;
        }

        // Return Data
        return view('admin.statistik', [
            'filter' => $filter, // Untuk active state tombol
            'labels' => $trendLabels, // Sumbu X (Tanggal)
            'trendScores' => $trendScores, // Data Line Chart
            'sentimentTrend' => [ // Data Stacked Bar
                'positif' => $sentimentData['Positif'],
                'netral'  => $sentimentData['Netral'],
                'negatif' => $sentimentData['Negatif'],
            ],
            'topKeywords' => $topKeywords
        ]);
    }
}

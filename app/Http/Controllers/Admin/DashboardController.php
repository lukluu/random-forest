<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // --- MOCK DATA (Data Contoh) ---
        // Nanti Anda ganti dengan query database sungguhan: 
        // misal: Survey::count();

        $stats = [
            'total_responden' => 128,
            'avg_score' => 4.6, // Skala 5
            'sentiment_dominant' => 'Positif',
            'sentiment_percentage' => 85, // 85% Positif
        ];

        // Data untuk Grafik (Rata-rata per kategori)
        $chartData = [
            'labels' => ['Rasa', 'Harga', 'Pelayanan', 'Kebersihan', 'Keramahan'],
            'scores' => [4.8, 4.2, 4.5, 4.7, 4.9]
        ];

        // Data Tabel Terbaru
        $recentSurveys = [
            ['name' => 'Andi Wijaya', 'date' => '31 Jan 2026', 'score' => 5, 'sentiment' => 'Positif', 'review' => 'Kopi mantap, tempat cozy banget!'],
            ['name' => 'Siti Nurhaliza', 'date' => '31 Jan 2026', 'score' => 4, 'sentiment' => 'Positif', 'review' => 'Pelayanan ramah, tapi wifi agak lambat.'],
            ['name' => 'Budi Santoso', 'date' => '30 Jan 2026', 'score' => 3, 'sentiment' => 'Netral', 'review' => 'Harga lumayan mahal untuk porsi segini.'],
            ['name' => 'Citra Kirana', 'date' => '29 Jan 2026', 'score' => 2, 'sentiment' => 'Negatif', 'review' => 'Meja kotor saat saya datang.'],
            ['name' => 'Eko Patrio', 'date' => '28 Jan 2026', 'score' => 5, 'sentiment' => 'Positif', 'review' => 'Best coffee in town!'],
        ];

        return view('admin.dashboard', compact('stats', 'chartData', 'recentSurveys'));
    }
    /**
     * Menampilkan Halaman Statistik Detail
     */
    public function statistics()
    {
        // 1. Data Trend Kepuasan (Line Chart) - Contoh data 6 bulan terakhir
        $trendData = [
            'labels' => ['Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan'],
            'scores' => [3.8, 4.0, 4.2, 4.1, 4.5, 4.6]
        ];

        // 2. Data Radar Chart (Perbandingan Aspek vs Target)
        $radarData = [
            'categories' => ['Rasa', 'Harga', 'Pelayanan', 'Kebersihan', 'Keramahan'],
            'current' => [4.8, 4.2, 4.5, 4.7, 4.9], // Skor saat ini
            'target' => [5.0, 4.5, 4.8, 5.0, 5.0]     // Target perusahaan
        ];

        // 3. Data Komposisi Sentimen Bulanan (Stacked Bar)
        $sentimentTrend = [
            'positif' => [40, 45, 50, 48, 60, 85],
            'netral'  => [30, 30, 30, 32, 25, 10],
            'negatif' => [30, 25, 20, 20, 15, 5],
        ];

        // 4. Kata Kunci Paling Sering Muncul (Word Cloud Simulation)
        $topKeywords = [
            ['word' => 'Enak', 'count' => 120, 'sentiment' => 'Positif'],
            ['word' => 'Ramah', 'count' => 98, 'sentiment' => 'Positif'],
            ['word' => 'Mahal', 'count' => 45, 'sentiment' => 'Negatif'],
            ['word' => 'Bersih', 'count' => 80, 'sentiment' => 'Positif'],
            ['word' => 'Lama', 'count' => 30, 'sentiment' => 'Negatif'],
            ['word' => 'Wifi', 'count' => 50, 'sentiment' => 'Netral'],
        ];

        return view('admin.statistik', compact('trendData', 'radarData', 'sentimentTrend', 'topKeywords'));
    }
}

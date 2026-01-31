<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SystemTestController extends Controller
{
    public function index()
    {
        return view('admin.uji-sistem.index');
    }

    public function process(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        // 1. Baca File Excel ke dalam Array
        // Asumsi: Kolom pertama (A) adalah ulasan
        $dataArray = Excel::toArray([], $request->file('file'))[0];

        $results = [];
        $stats = [
            'total' => 0,
            'positif' => 0,
            'netral' => 0,
            'negatif' => 0,
            'avg_confidence' => 0
        ];
        $totalConf = 0;

        // 2. Loop setiap baris (Skip header jika ada)
        foreach ($dataArray as $index => $row) {
            // Skip baris pertama jika itu judul kolom (opsional, sesuaikan kebutuhan)
            if ($index == 0 && strtolower($row[0]) == 'ulasan') continue;

            $ulasan = $row[0] ?? null; // Ambil kolom A

            if (!$ulasan) continue;

            // 3. Tembak API Flask
            $sentiment = 'Error';
            $confidence = 0;

            try {
                $response = Http::timeout(2)->post('http://127.0.0.1:5000/predict', [
                    'ulasan' => $ulasan
                ]);

                if ($response->successful()) {
                    $json = $response->json();
                    $sentiment = ucfirst($json['sentimen']);

                    if (isset($json['probabilitas'])) {
                        $confidence = max($json['probabilitas']) * 100;
                    }
                }
            } catch (\Exception $e) {
                $sentiment = 'API Error';
            }

            // 4. Hitung Statistik
            $stats['total']++;
            $totalConf += $confidence;
            if ($sentiment == 'Positif') $stats['positif']++;
            elseif ($sentiment == 'Netral') $stats['netral']++;
            elseif ($sentiment == 'Negatif') $stats['negatif']++;

            // 5. Simpan Hasil
            $results[] = [
                'ulasan' => $ulasan,
                'sentiment' => $sentiment,
                'confidence' => $confidence
            ];
        }

        // Rata-rata confidence
        if ($stats['total'] > 0) {
            $stats['avg_confidence'] = $totalConf / $stats['total'];
        }

        return view('admin.uji-sistem.index', compact('results', 'stats'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataSurveyController extends Controller
{
    /**
     * Menampilkan Daftar Seluruh Data Survey
     */
    public function index()
    {
        // MOCK DATA (Data Contoh yang banyak)
        // Nanti diganti: Survey::orderBy('created_at', 'desc')->paginate(10);

        $surveys = [
            [
                'id' => 1,
                'date' => '31 Jan 2026 14:30',
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'avg_score' => 4.8,
                'sentiment' => 'Positif',
                'review' => 'Tempatnya sangat nyaman, kopi susunya juara! Pasti bakal balik lagi.',
                'details' => ['Rasa' => 5, 'Harga' => 4, 'Pelayanan' => 5, 'Kebersihan' => 5]
            ],
            [
                'id' => 2,
                'date' => '31 Jan 2026 10:15',
                'name' => 'Siti Aminah',
                'email' => 'siti.am@yahoo.com',
                'avg_score' => 3.2,
                'sentiment' => 'Netral',
                'review' => 'Lumayan sih, tapi AC-nya kurang dingin siang ini.',
                'details' => ['Rasa' => 4, 'Harga' => 3, 'Pelayanan' => 3, 'Kebersihan' => 3]
            ],
            [
                'id' => 3,
                'date' => '30 Jan 2026 19:45',
                'name' => 'Joko Anwar',
                'email' => 'joko@studio.com',
                'avg_score' => 1.5,
                'sentiment' => 'Negatif',
                'review' => 'Pelayan judes, meja kotor bekas orang sebelumnya tidak dibersihkan.',
                'details' => ['Rasa' => 2, 'Harga' => 2, 'Pelayanan' => 1, 'Kebersihan' => 1]
            ],
            [
                'id' => 4,
                'date' => '29 Jan 2026 08:20',
                'name' => 'Rina Nose',
                'email' => 'rina@mail.com',
                'avg_score' => 5.0,
                'sentiment' => 'Positif',
                'review' => 'Perfect! Baristanya ramah banget.',
                'details' => ['Rasa' => 5, 'Harga' => 5, 'Pelayanan' => 5, 'Kebersihan' => 5]
            ],
            [
                'id' => 5,
                'date' => '28 Jan 2026 16:10',
                'name' => 'Doni Tata',
                'email' => 'doni.racing@mail.com',
                'avg_score' => 4.0,
                'sentiment' => 'Positif',
                'review' => 'Kopi enak, harga standar. Parkiran agak sempit.',
                'details' => ['Rasa' => 4, 'Harga' => 4, 'Pelayanan' => 4, 'Kebersihan' => 4]
            ],
        ];

        return view('admin.data_survey', compact('surveys'));
    }
}

<?php

namespace App\Exports;

use App\Models\UserSurvey;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UserSurveyExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $search;

    // Terima parameter search dari Controller agar hasil export sesuai filter
    public function __construct($search = null)
    {
        $this->search = $search;
    }

    public function query()
    {
        $query = UserSurvey::query();

        // Terapkan logika pencarian yang sama dengan tampilan tabel
        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('email', 'like', "%{$this->search}%")
                ->orWhere('review', 'like', "%{$this->search}%");
        }

        return $query->orderBy('created_at', 'desc');
    }

    // Judul Kolom (Header)
    public function headings(): array
    {
        return [
            'ID',
            'Tanggal Masuk',
            'Nama Pelanggan',
            'Email',
            'Skor Rata-rata',
            'Sentimen',
            'Review',
            'Rasa',
            'Harga',
            'Pelayanan',
            'Kebersihan',
            'Keramahan',
        ];
    }

    // Mapping Data (Apa yang mau ditampilkan di tiap baris)
    public function map($survey): array
    {
        return [
            $survey->id,
            $survey->created_at->translatedFormat('d F Y H:i'), // Format Tanggal Cantik
            $survey->name,
            $survey->email,
            number_format($survey->score_average, 1),
            $survey->sentiment,
            $survey->review,
            $survey->score_rasa,
            $survey->score_harga,
            $survey->score_pelayanan,
            $survey->score_kebersihan,
            $survey->score_keramahan,
        ];
    }

    // Styling (Bikin Header Tebal)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

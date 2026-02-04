@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#5D4037]">Detail Pengujian #{{ $data->id }}</h1>
            <p class="text-sm text-gray-500">Dilakukan pada {{ $data->created_at->translatedFormat('d F Y, H:i') }} WIB</p>
        </div>
        <a href="{{ route('admin.uji-sistem.history') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-600 hover:text-[#5D4037] hover:border-[#5D4037] rounded-lg transition-colors flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- KOLOM KIRI: INPUT DATA --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Kartu Ulasan --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Input Ulasan</h3>
                <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 relative">
                    <i class="fa-solid fa-quote-left absolute top-4 left-4 text-gray-200 text-4xl"></i>
                    <p class="text-gray-700 italic relative z-10 leading-relaxed text-lg">
                        "{{ $data->review }}"
                    </p>
                </div>
            </div>

            {{-- Kartu Metadata Slider (FIXED) --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Metadata Penilaian Visual</h3>

                <div class="space-y-5">
                    @php
                    // Definisikan array asosiatif: Label => Nilai dari DB
                    $aspects = [
                    'Cita Rasa' => $data->score_rasa,
                    'Harga' => $data->score_harga,
                    'Pelayanan' => $data->score_pelayanan,
                    'Kebersihan' => $data->score_kebersihan,
                    'Keramahan Staff'=> $data->score_keramahan,
                    ];
                    @endphp

                    @foreach($aspects as $label => $value)
                    <div class="flex items-center gap-4">
                        {{-- Label --}}
                        <span class="w-32 text-sm font-medium text-gray-600">{{ $label }}</span>

                        {{-- Progress Bar Container --}}
                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                            {{-- Bar Isi --}}
                            {{--
                                PERBAIKAN: 
                                1. Menggunakan bg-[#8C5E3C] (kode hex) agar warna pasti keluar.
                                2. Menggunakan syntax {{ }} untuk style width agar lebih bersih.
                            --}}
                            <div class="h-full bg-[#8C5E3C] rounded-full transition-all duration-500 ease-out"
                                style="width: {{ ($value / 5) * 100 }}%">
                            </div>
                        </div>

                        {{-- Nilai Angka --}}
                        <span class="w-8 text-sm font-bold text-[#8C5E3C] text-right">
                            {{ $value }}
                        </span>
                    </div>
                    @endforeach
                </div>

                {{-- Rata-rata --}}
                <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                    <span class="text-sm text-gray-500 font-medium">Rata-rata Skor Visual</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black text-gray-800">{{ number_format($data->score_average, 1) }}</span>
                        <span class="text-sm text-gray-400 font-medium">/ 5.0</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: HASIL AI --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Kartu Status Match --}}
            @if($data->ground_truth == $data->sentiment)
            <div class="bg-green-50 p-6 rounded-2xl border border-green-200 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-green-600 text-2xl shadow-sm border border-green-100">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 class="text-green-800 font-bold text-lg">Prediksi Akurat</h3>
                <p class="text-green-600 text-sm mt-1">Hasil AI cocok dengan label manual.</p>
            </div>
            @else
            <div class="bg-red-50 p-6 rounded-2xl border border-red-200 text-center">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-red-600 text-2xl shadow-sm border border-red-100">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <h3 class="text-red-800 font-bold text-lg">Prediksi Meleset</h3>
                <p class="text-red-600 text-sm mt-1">AI memprediksi berbeda dengan label manual.</p>
            </div>
            @endif

            {{-- Perbandingan Side-by-Side --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-6 text-center">Perbandingan Hasil</h3>

                <div class="flex items-center justify-center gap-4">
                    {{-- Sisi Kiri: Manual --}}
                    <div class="flex-1 text-center">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block mb-2">Ground Truth</span>
                        @php
                        $gtColor = match($data->ground_truth) {
                        'Positif' => 'bg-green-100 text-green-700 border-green-200',
                        'Negatif' => 'bg-red-100 text-red-700 border-red-200',
                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                        };
                        @endphp
                        <div class="py-2 px-3 rounded-lg border {{ $gtColor }} font-bold text-sm">
                            {{ $data->ground_truth }}
                        </div>
                    </div>

                    <div class="text-gray-300">
                        <i class="fa-solid fa-right-left"></i>
                    </div>

                    {{-- Sisi Kanan: AI --}}
                    <div class="flex-1 text-center">
                        <span class="text-[10px] uppercase font-bold text-gray-400 block mb-2">Prediksi AI</span>
                        @php
                        $aiColor = match($data->sentiment) {
                        'Positif' => 'bg-green-100 text-green-700 border-green-200',
                        'Negatif' => 'bg-red-100 text-red-700 border-red-200',
                        default => 'bg-gray-100 text-gray-700 border-gray-200'
                        };
                        @endphp
                        <div class="py-2 px-3 rounded-lg border {{ $aiColor }} font-bold text-sm">
                            {{ $data->sentiment }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Probabilitas Detail --}}
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">Probabilitas Model</h3>
                <div class="space-y-4">
                    @if(is_array($data->probabilities) || is_object($data->probabilities))
                    @foreach($data->probabilities as $label => $prob)
                    <div>
                        <div class="flex justify-between text-xs mb-1">
                            <span class="capitalize text-gray-600 font-medium">{{ $label }}</span>
                            <span class="font-bold text-gray-800">{{ number_format($prob * 100, 1) }}%</span>
                        </div>
                        <div class="w-full h-2 bg-gray-100 rounded-full overflow-hidden">
                            @php
                            $barColor = match(strtolower($label)) {
                            'positif' => 'bg-green-500',
                            'negatif' => 'bg-red-500',
                            'netral' => 'bg-gray-400',
                            default => 'bg-gray-400'
                            };
                            @endphp
                            <div class="h-full {{ $barColor }}" style="width: {{ $prob * 100 }}%"></div>
                        </div>
                    </div>
                    @endforeach
                    @else
                    <p class="text-xs text-gray-400 italic text-center">Data probabilitas tidak tersedia.</p>
                    @endif
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                    <span class="text-xs text-gray-400 uppercase font-bold tracking-wider">Confidence Score</span>
                    <span class="text-2xl font-black text-coffee-accent block mt-1">
                        {{ number_format((float)$data->confidence_score, 1) }}%
                    </span>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
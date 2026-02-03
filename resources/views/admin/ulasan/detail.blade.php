@extends('layouts.admin')

@section('title', 'Detail Survey')

@section('content')
{{-- HEADER & TOMBOL KEMBALI --}}
<div class="mb-8 flex items-center justify-between">
    <div class="flex items-center gap-4">
        <a href="{{ route('data-survey') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-500 hover:text-[#8C5E3C] hover:border-[#8C5E3C] transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-[#5D4037]">Detail Ulasan Pelanggan</h2>
            <p class="text-sm text-gray-500">ID Survei: #{{ $survey->id }} • Masuk pada {{ $survey->created_at->translatedFormat('d F Y, H:i') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- KOLOM KIRI: INFO USER & RINGKASAN --}}
    <div class="space-y-6">

        {{-- KARTU PROFIL --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
            <h3 class="font-bold text-[#5D4037] mb-4 text-sm uppercase tracking-wide">Informasi Pelanggan</h3>

            <div class="flex items-center gap-4 mb-6">
                <div class="w-14 h-14 bg-[#F8F5F2] rounded-full flex items-center justify-center text-[#8C5E3C] font-bold text-xl">
                    {{ substr($survey->name, 0, 1) }}
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">{{ $survey->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $survey->email }}</p>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-4 space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Status Member</span>
                    <span class="font-medium text-gray-700">Guest / Umum</span>
                </div>
            </div>
        </div>

        {{-- KARTU RINGKASAN SKOR --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC] text-center">
            <h3 class="font-bold text-[#5D4037] mb-2 text-sm uppercase tracking-wide">Skor Keseluruhan</h3>

            <div class="my-6 relative inline-flex items-center justify-center">
                <svg class="w-32 h-32 transform -rotate-90">
                    <circle cx="64" cy="64" r="56" stroke="#F3F4F6" stroke-width="8" fill="none" />
                    <circle cx="64" cy="64" r="56" stroke="{{ $survey->score_average >= 4 ? '#10B981' : ($survey->score_average >= 3 ? '#F59E0B' : '#EF4444') }}" stroke-width="8" fill="none" stroke-dasharray="351" stroke-dashoffset="{{ 351 - (351 * $survey->score_average / 5) }}" stroke-linecap="round" />
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="text-4xl font-bold text-[#5D4037]">{{ number_format($survey->score_average, 1) }}</span>
                    <span class="text-xs text-gray-400">dari 5.0</span>
                </div>
            </div>

            <div>
                @if($survey->sentiment == 'Positif')
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold bg-green-100 text-green-700">
                    <span class="w-2 h-2 rounded-full bg-green-600"></span> Sentimen Positif
                </span>
                @elseif($survey->sentiment == 'Netral')
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold bg-gray-100 text-gray-700">
                    <span class="w-2 h-2 rounded-full bg-gray-500"></span> Sentimen Netral
                </span>
                @else
                <span class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-full text-sm font-bold bg-red-100 text-red-700">
                    <span class="w-2 h-2 rounded-full bg-red-600"></span> Sentimen Negatif
                </span>
                @endif

                @if($survey->confidence_score)
                <p class="text-xs text-gray-400 mt-2">AI Confidence: {{ $survey->confidence_score }}%</p>
                @endif
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN: DETAIL NILAI & REVIEW --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- DETAIL PENILAIAN (BARS) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
            <h3 class="font-bold text-[#5D4037] mb-6 text-lg">Rincian Penilaian Aspek</h3>

            <div class="space-y-5">
                @php
                $aspects = [
                'Rasa Menu' => $survey->score_rasa,
                'Harga' => $survey->score_harga,
                'Kualitas Pelayanan' => $survey->score_pelayanan,
                'Kebersihan Tempat' => $survey->score_kebersihan,
                'Keramahan Staff' => $survey->score_keramahan,
                ];
                @endphp

                @foreach($aspects as $label => $score)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                        <span class="text-sm font-bold text-[#5D4037]">{{ $score }} / 5</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5">
                        <div class="h-2.5 rounded-full {{ $score >= 4 ? 'bg-[#8C5E3C]' : ($score == 3 ? 'bg-yellow-400' : 'bg-red-400') }}"
                            style="width: <?php echo ($score / 5) * 100; ?>%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- TEXT REVIEW --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
            <div class="flex items-center gap-2 mb-4">
                <svg class="w-5 h-5 text-[#8C5E3C]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                <h3 class="font-bold text-[#5D4037] text-lg">Ulasan Tertulis</h3>
            </div>

            <div class="bg-[#FDFBF9] p-5 rounded-xl border border-[#E6E0DC] relative">
                <svg class="absolute top-4 left-4 w-8 h-8 text-[#E6E0DC] opacity-50 transform -scale-x-100" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14.017 21L14.017 18C14.017 16.8954 13.1216 16 12.017 16H9C9.02329 12.1932 11.2319 12.1648 11.2319 12.1648C11.597 12.1328 11.8385 11.7891 11.7258 11.4398C11.3855 10.384 10.3831 6 5.82361 6C5.55835 6 5.31298 6.13629 5.17647 6.36394C4.43717 7.59725 4 9.17725 4 11C4 16.5228 8.47715 21 14.017 21ZM21.017 21L21.017 18C21.017 16.8954 20.1216 16 19.017 16H16C16.0233 12.1932 18.2319 12.1648 18.2319 12.1648C18.597 12.1328 18.8385 11.7891 18.7258 11.4398C18.3855 10.384 17.3831 6 12.8236 6C12.5584 6 12.313 6.13629 12.1765 6.36394C11.4372 7.59725 11 9.17725 11 11C11 16.5228 15.4772 21 21.017 21Z"></path>
                </svg>
                <p class="text-gray-700 italic leading-relaxed pl-8 relative z-10">
                    "{{ $survey->review }}"
                </p>
            </div>
        </div>

    </div>
</div>
@endsection
@extends('layouts.app')

@section('title', 'Hasil Analisis - Kopi Kita')

@section('content')
<div class="w-full max-w-4xl mx-auto py-8 px-4">

    <div class="text-center mb-10">
        <h1 class="text-3xl md:text-4xl font-bold text-coffee-primary mb-2">Hasil Analisis Survey</h1>
        <p class="text-gray-500">Terima kasih telah memberikan masukan berharga Anda.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl border border-[#E6E0DC] overflow-hidden mb-12 relative">
        <div class="absolute top-0 left-0 w-full h-2 bg-coffee-accent"></div>

        <div class="p-8 md:p-12 text-center">
            <h2 class="text-lg font-semibold text-coffee-dark mb-4 uppercase tracking-wider">Sentimen Ulasan Anda</h2>

            {{-- Menyiapkan variabel Confidence untuk Kartu Utama --}}
            @php
            $conf = $currentResult['confidence_score'] ?? 0;
            @endphp

            @if($currentResult['sentiment'] == 'Positif')
            <div class="inline-block p-6 rounded-full bg-green-50 border-2 border-green-100 mb-6">
                <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-5xl font-bold text-green-700 mb-2">POSITIF</h3>

            {{-- 1. BADGE CONFIDENCE (POSITIF) --}}
            @if($conf > 0)
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Akurasi AI: {{ number_format($conf, 1) }}%
                </span>
            </div>
            @endif

            <p class="text-green-600/80 max-w-md mx-auto">Terima kasih! Kami sangat senang Anda menikmati pengalaman di Kopi Kita.</p>

            @elseif($currentResult['sentiment'] == 'Netral')
            <div class="inline-block p-6 rounded-full bg-gray-50 border-2 border-gray-100 mb-6">
                <svg class="w-20 h-20 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-5xl font-bold text-gray-600 mb-2">NETRAL</h3>

            {{-- 1. BADGE CONFIDENCE (NETRAL) --}}
            @if($conf > 0)
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700 border border-gray-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Akurasi AI: {{ number_format($conf, 1) }}%
                </span>
            </div>
            @endif

            <p class="text-gray-500 max-w-md mx-auto">Terima kasih atas masukannya. Kami akan terus berusaha meningkatkan layanan kami.</p>

            @else
            <div class="inline-block p-6 rounded-full bg-red-50 border-2 border-red-100 mb-6">
                <svg class="w-20 h-20 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-5xl font-bold text-red-600 mb-2">NEGATIF</h3>

            {{-- 1. BADGE CONFIDENCE (NEGATIF) --}}
            @if($conf > 0)
            <div class="mb-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Akurasi AI: {{ number_format($conf, 1) }}%
                </span>
            </div>
            @endif

            <p class="text-red-500/80 max-w-md mx-auto">Mohon maaf atas ketidaknyamanan Anda. Kami akan segera mengevaluasi layanan kami.</p>
            @endif

            <div class="mt-8 p-4 bg-[#FDFBF9] rounded-xl border border-[#E6E0DC] inline-block w-full max-w-2xl">
                <p class="text-coffee-dark italic">"{{ $currentResult['review'] }}"</p>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
            <h2 class="text-2xl font-bold text-coffee-primary">Riwayat Survey Terbaru</h2>
            <span class="text-sm text-gray-500">Menampilkan 5 data terakhir</span>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-[#E6E0DC] overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-[#FDFBF9] border-b border-[#E6E0DC] text-coffee-dark">
                            <th class="px-6 py-4 font-semibold text-sm">Tanggal</th>
                            <th class="px-6 py-4 font-semibold text-sm">Nama Pelanggan</th>
                            <th class="px-6 py-4 font-semibold text-sm">Skor Rata-rata</th>
                            <th class="px-6 py-4 font-semibold text-sm">Review</th>
                            <th class="px-6 py-4 font-semibold text-sm text-center">Hasil AI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($history as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($item['created_at'])->format('d F Y') }}
                            </td>


                            <td class="px-6 py-4 text-sm font-medium text-coffee-dark">{{ $item['name'] }}</td>
                            <td class="px-6 py-4 align-top">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center space-x-0.5">
                                        @php
                                        $skor = $item['score_average'] ?? $item['score_avg'] ?? 0;
                                        $bintangBulat = round($skor);
                                        @endphp

                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $bintangBulat ? 'text-yellow-400 fill-yellow-400' : 'text-gray-200 fill-gray-200' }}"
                                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.545.044.757.74.34 1.146l-4.155 4.073a.563.563 0 00-.163.506l1.241 5.378c.13.56-.475.986-.975.68l-4.71-2.933a.563.563 0 00-.572 0l-4.71 2.933c-.5.306-1.105-.12-.975-.68l1.241-5.378a.563.563 0 00-.163-.506l-4.155-4.073c-.417-.406-.205-1.102.34-1.146l5.518-.442a.563.563 0 00.475-.345L11.48 3.499z" />
                                            </svg>
                                            @endfor
                                    </div>

                                    <span class="text-xs font-semibold text-gray-500 ml-0.5">
                                        {{ number_format($skor, 1) }} <span class="font-normal text-gray-400">/ 5.0</span>
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 italic truncate max-w-xs">
                                "{{ Str::limit($item['review'], 40) }}"
                            </td>

                            {{-- 2. KOLOM HASIL AI (DENGAN CONFIDENCE SCORE) --}}
                            <td class="px-6 py-4 text-center align-middle">
                                <div class="flex flex-col items-center gap-1">
                                    {{-- Badge Sentimen --}}
                                    @if($item['sentiment'] == 'Positif')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Positif
                                    </span>
                                    @elseif($item['sentiment'] == 'Netral')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Netral
                                    </span>
                                    @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Negatif
                                    </span>
                                    @endif

                                    {{-- Teks Persentase Akurasi di Tabel --}}
                                    @php
                                    $confTable = $item['confidence_score'] ?? 0;
                                    @endphp
                                    @if($confTable > 0)
                                    <span class="text-[10px] font-medium text-gray-400">
                                        Akurasi: {{ number_format($confTable, 1) }}%
                                    </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(count($history) == 0)
            <div class="p-8 text-center text-gray-400">Belum ada riwayat survey.</div>
            @endif
        </div>
    </div>

    <div class="mt-12 text-center flex justify-center gap-4">
        <a href="{{ route('survey.start') }}" class="px-6 py-3 bg-white border border-[#E6E0DC] text-coffee-dark font-semibold rounded-xl hover:bg-gray-50 transition-colors shadow-sm">
            Isi Survey Lagi
        </a>
        <a href="{{ url('/') }}" class="px-6 py-3 bg-coffee-accent text-white font-semibold rounded-xl hover:bg-coffee-accentHover transition-colors shadow-lg shadow-coffee-accent/20">
            Kembali ke Beranda
        </a>
    </div>

</div>
@endsection
@extends('layouts.admin')

@section('title', 'Laporan Statistik')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#5D4037]">Laporan Statistik</h2>
        <p class="text-gray-500 mt-1">Analisis performa berdasarkan data aktual survei.</p>
    </div>

    {{-- TOMBOL FILTER --}}
    <div class="flex bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
        <a href="{{ route('dashboard.statistics', ['filter' => 'daily']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition-all 
           {{ $filter == 'daily' ? 'bg-[#8C5E3C] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
            Harian
        </a>
        <a href="{{ route('dashboard.statistics', ['filter' => 'weekly']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition-all 
           {{ $filter == 'weekly' ? 'bg-[#8C5E3C] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
            Mingguan
        </a>
        <a href="{{ route('dashboard.statistics', ['filter' => 'monthly']) }}"
            class="px-4 py-2 text-sm font-medium rounded-md transition-all 
           {{ $filter == 'monthly' ? 'bg-[#8C5E3C] text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50' }}">
            Bulanan
        </a>
    </div>
</div>

{{-- CHART 1: TREN KEPUASAN (TETAP FULL WIDTH) --}}
<div class="w-full bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC] mb-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h3 class="font-bold text-[#5D4037] text-lg">Tren Skor Kepuasan</h3>
            <p class="text-xs text-gray-400">Rata-rata skor (Skala 1-5)</p>
        </div>

        @php
        $lastScore = !empty($trendScores) ? end($trendScores) : 0;
        $prevScore = count($trendScores) > 1 ? prev($trendScores) : 0;
        $isUp = $lastScore >= $prevScore;
        @endphp

        @if(count($trendScores) > 1)
        <div class="flex items-center gap-2 text-sm {{ $isUp ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }} px-3 py-1 rounded-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $isUp ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}"></path>
            </svg>
            <span>{{ $isUp ? 'Tren Positif' : 'Tren Menurun' }}</span>
        </div>
        @endif
    </div>

    @if(empty($trendScores))
    <div class="h-[300px] flex items-center justify-center text-gray-400">Belum ada data survei pada periode ini.</div>
    @else
    <div id="chart-trend" class="w-full h-[300px]"></div>
    @endif
</div>

{{-- GRID LAYOUT: 2 KOLOM (VOLUME SENTIMEN & KATA KUNCI) --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

    {{-- KIRI: CHART SENTIMEN --}}
    <div class="w-full bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-[#5D4037] text-lg mb-1">Volume & Sentimen</h3>
                <p class="text-sm text-gray-400">Komposisi sentimen ulasan</p>
            </div>
        </div>

        @if(empty($trendScores))
        <div class="h-[300px] flex items-center justify-center text-gray-400">Belum ada data.</div>
        @else
        <div id="chart-sentiment-trend"></div>
        @endif
    </div>

    {{-- KANAN: WORD CLOUD --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-[#5D4037] text-lg mb-1">Kata Kunci Populer</h3>
                <p class="text-sm text-gray-400">Apa yang sering dibicarakan?</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 content-start h-[300px] overflow-y-auto pr-2 custom-scrollbar">
            @forelse($topKeywords as $key)
            @php
            $bgColor = match($key['sentiment']) {
            'Positif' => 'bg-green-50 text-green-700 border-green-100',
            'Negatif' => 'bg-red-50 text-red-700 border-red-100',
            default => 'bg-gray-50 text-gray-700 border-gray-100',
            };
            // Sedikit penyesuaian ukuran font agar muat di setengah layar
            $size = $key['count'] > 5 ? 'text-base px-4 py-1.5' : 'text-xs px-2.5 py-1';
            @endphp

            <div class="{{ $bgColor }} border rounded-full font-bold {{ $size }} cursor-default transition-transform hover:scale-105 shadow-sm flex items-center gap-2 h-fit">
                {{ $key['word'] }}
                <span class="opacity-60 text-[10px] bg-white/50 px-1.5 rounded-full">{{ $key['count'] }}</span>
            </div>
            @empty
            <div class="w-full h-full flex items-center justify-center text-gray-400 italic">
                Belum cukup data ulasan untuk dianalisis.
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- SCRIPT GRAPH (TIDAK ADA PERUBAHAN LOGIKA, HANYA RENDER) --}}
<script>
    const labels = @json($labels);
    const trendScores = @json($trendScores);
    const sentimentPos = @json($sentimentTrend['positif']);
    const sentimentNeu = @json($sentimentTrend['netral']);
    const sentimentNeg = @json($sentimentTrend['negatif']);

    // 1. Line Chart
    if (document.querySelector("#chart-trend") && trendScores.length > 0) {
        var optionsTrend = {
            series: [{
                name: "Skor Rata-rata",
                data: trendScores
            }],
            chart: {
                height: 300, // Sedikit diperkecil
                type: 'area',
                fontFamily: 'Instrument Sans',
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#8C5E3C'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: labels,
                tooltip: {
                    enabled: false
                }
            },
            yaxis: {
                min: 0,
                max: 5,
                tickAmount: 5
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " / 5.0"
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#chart-trend"), optionsTrend).render();
    }

    // 2. Stacked Bar Chart
    if (document.querySelector("#chart-sentiment-trend") && trendScores.length > 0) {
        var optionsStack = {
            series: [{
                    name: 'Positif',
                    data: sentimentPos
                },
                {
                    name: 'Netral',
                    data: sentimentNeu
                },
                {
                    name: 'Negatif',
                    data: sentimentNeg
                }
            ],
            chart: {
                type: 'bar',
                height: 300, // Samakan tinggi dengan sebelahnya
                stacked: true,
                fontFamily: 'Instrument Sans',
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 2,
                    columnWidth: '60%'
                }, // Column width diperbesar sedikit
            },
            colors: ['#10B981', '#9CA3AF', '#EF4444'],
            xaxis: {
                categories: labels
            },
            legend: {
                position: 'top',
                fontSize: '12px'
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " Ulasan"
                    }
                }
            }
        };
        new ApexCharts(document.querySelector("#chart-sentiment-trend"), optionsStack).render();
    }
</script>

<style>
    /* Styling tambahan untuk scrollbar di box keywords */
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 4px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
</style>
@endsection
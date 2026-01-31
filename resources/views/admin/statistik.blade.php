@extends('layouts.admin')

@section('title', 'Laporan Statistik')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#5D4037]">Laporan Statistik</h2>
        <p class="text-gray-500 mt-1">Analisis mendalam performa layanan Kopi Kita.</p>
    </div>

    <div class="flex bg-white p-1 rounded-lg border border-gray-200 shadow-sm">
        <button class="px-4 py-2 text-sm font-medium rounded-md bg-[#8C5E3C] text-white shadow-sm">6 Bulan</button>
        <button class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:bg-gray-50">Tahun Ini</button>
        <button class="px-4 py-2 text-sm font-medium rounded-md text-gray-500 hover:bg-gray-50">Semua</button>
    </div>
</div>

<div class="w-full bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC] mb-8">
    <div class="flex justify-between items-center mb-6">
        <h3 class="font-bold text-[#5D4037] text-lg">Tren Kepuasan Pelanggan</h3>
        <div class="flex items-center gap-2 text-sm text-green-600 bg-green-50 px-3 py-1 rounded-full">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
            <span>Meningkat signifikan</span>
        </div>
    </div>
    <div id="chart-trend" class="w-full h-[350px]"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <h3 class="font-bold text-[#5D4037] text-lg mb-2">Analisis Performa Aspek</h3>
        <p class="text-sm text-gray-400 mb-6">Perbandingan skor saat ini vs target perusahaan</p>
        <div id="chart-radar" class="flex justify-center"></div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <h3 class="font-bold text-[#5D4037] text-lg mb-2">Tren Sentimen</h3>
        <p class="text-sm text-gray-400 mb-6">Distribusi sentimen ulasan per bulan</p>
        <div id="chart-sentiment-trend"></div>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
    <h3 class="font-bold text-[#5D4037] text-lg mb-6">Kata Kunci Populer (Top Keywords)</h3>

    <div class="flex flex-wrap gap-4">
        @foreach($topKeywords as $key)
        @php
        // Logika warna berdasarkan sentimen
        $bgColor = match($key['sentiment']) {
        'Positif' => 'bg-green-50 text-green-700 border-green-100',
        'Negatif' => 'bg-red-50 text-red-700 border-red-100',
        default => 'bg-gray-50 text-gray-700 border-gray-100',
        };
        // Ukuran font berdasarkan jumlah (simulasi word cloud sederhana)
        $size = $key['count'] > 100 ? 'text-2xl px-6 py-3' : ($key['count'] > 50 ? 'text-lg px-4 py-2' : 'text-sm px-3 py-1');
        @endphp

        <div class="{{ $bgColor }} border rounded-full font-bold {{ $size }} cursor-default transition-transform hover:scale-105 shadow-sm flex items-center gap-2">
            {{ $key['word'] }}
            <span class="opacity-60 text-xs bg-white/50 px-1.5 rounded-full">{{ $key['count'] }}</span>
        </div>
        @endforeach
    </div>
</div>

<script>
    // 1. CONFIG: LINE CHART (Trend)
    var optionsTrend = {
        series: [{
            name: "Skor Rata-rata",
            data: @json($trendData['scores'])
        }],
        chart: {
            height: 350,
            type: 'area', // Area chart terlihat lebih elegan
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
        colors: ['#8C5E3C'], // Warna Cokelat Utama
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.1, // Fade effect ke bawah
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: @json($trendData['labels']),
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

    // 2. CONFIG: RADAR CHART (Aspek vs Target)
    var optionsRadar = {
        series: [{
            name: 'Skor Saat Ini',
            data: @json($radarData['current'])
        }, {
            name: 'Target',
            data: @json($radarData['target'])
        }],
        chart: {
            height: 350,
            type: 'radar',
            fontFamily: 'Instrument Sans',
            toolbar: {
                show: false
            }
        },
        colors: ['#8C5E3C', '#E5E7EB'], // Cokelat vs Abu-abu (Target)
        stroke: {
            width: 2
        },
        fill: {
            opacity: 0.2
        },
        markers: {
            size: 4
        },
        xaxis: {
            categories: @json($radarData['categories']),
            labels: {
                style: {
                    colors: ['#5D4037', '#5D4037', '#5D4037', '#5D4037', '#5D4037'],
                    fontSize: '13px',
                    fontFamily: 'Instrument Sans',
                    fontWeight: 600
                }
            }
        },
        yaxis: {
            show: false,
            max: 5
        }
    };
    new ApexCharts(document.querySelector("#chart-radar"), optionsRadar).render();

    // 3. CONFIG: STACKED BAR CHART (Sentimen)
    var optionsStack = {
        series: [{
            name: 'Positif',
            data: @json($sentimentTrend['positif'])
        }, {
            name: 'Netral',
            data: @json($sentimentTrend['netral'])
        }, {
            name: 'Negatif',
            data: @json($sentimentTrend['negatif'])
        }],
        chart: {
            type: 'bar',
            height: 350,
            stacked: true,
            fontFamily: 'Instrument Sans',
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                borderRadius: 0,
                columnWidth: '50%'
            },
        },
        colors: ['#10B981', '#9CA3AF', '#EF4444'], // Hijau, Abu, Merah
        xaxis: {
            categories: @json($trendData['labels']),
        },
        legend: {
            position: 'top'
        },
        fill: {
            opacity: 1
        }
    };
    new ApexCharts(document.querySelector("#chart-sentiment-trend"), optionsStack).render();
</script>
@endsection
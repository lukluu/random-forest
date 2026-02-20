@extends('layouts.admin')

@section('content')
<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-chart-line text-blue-600"></i> Analisis & Statistik Dataset
            </h1>
            <p class="text-sm text-gray-500">Melihat performa model dan tren sentimen dari ulasan yang diunggah.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.uji-dataset.riwayat') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-list"></i> Lihat Tabel Data
            </a>
            <a href="{{ route('admin.uji-dataset.index') }}" class="px-5 py-2.5 bg-blue-600 border border-blue-600 text-white hover:bg-blue-700 font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Upload
            </a>
        </div>
    </div>

    {{-- BARIS FILTER GLOBAL --}}
    <form action="{{ route('admin.uji-dataset.analisis') }}" method="GET" id="filterForm" class="bg-white p-5 rounded-2xl shadow-sm border border-gray-200 mb-6 flex flex-wrap gap-5 items-end">

        {{-- Filter Tahun --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tahun</label>
            <select name="year" onchange="document.getElementById('filterForm').submit()" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 px-4 py-2 cursor-pointer min-w-[130px] bg-gray-50 hover:bg-white transition-colors">
                <option value="">Semua Tahun</option>
                @foreach($availableYears as $y)
                <option value="{{ $y }}" {{ $yearFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>

        {{-- Filter Bulan --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Bulan</label>
            <select name="month" onchange="document.getElementById('filterForm').submit()" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 px-4 py-2 cursor-pointer min-w-[150px] bg-gray-50 hover:bg-white transition-colors">
                <option value="">Semua Bulan</option>
                @foreach(range(1, 12) as $m)
                @php $monthVal = str_pad($m, 2, '0', STR_PAD_LEFT); @endphp
                <option value="{{ $monthVal }}" {{ $monthFilter == $monthVal ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Filter Tampilan Grafik --}}
        <div>
            <label class="block text-xs font-bold text-gray-500 mb-1.5 uppercase tracking-wider">Tampilan Grafik Tren</label>
            <select name="range" onchange="document.getElementById('filterForm').submit()" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500/20 px-4 py-2 cursor-pointer min-w-[150px] bg-blue-50/50 text-blue-700 border-blue-200">
                <option value="weekly" {{ $range == 'weekly' ? 'selected' : '' }}>Tampilkan per Minggu</option>
                <option value="monthly" {{ $range == 'monthly' ? 'selected' : '' }}>Tampilkan per Bulan</option>
                <option value="yearly" {{ $range == 'yearly' ? 'selected' : '' }}>Tampilkan per Tahun</option>
            </select>
        </div>

        {{-- Tombol Reset --}}
        @if($yearFilter || $monthFilter || $range != 'monthly')
        <div class="ml-auto mb-2">
            <a href="{{ route('admin.uji-dataset.analisis') }}" class="text-sm text-red-500 hover:text-red-700 font-bold flex items-center gap-1">
                <i class="fa-solid fa-rotate-left"></i> Reset Filter
            </a>
        </div>
        @endif
    </form>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Dataset</p>
            <h3 class="text-3xl font-black text-gray-800">{{ number_format($totalData) }}</h3>
            <i class="fa-solid fa-database absolute -bottom-2 -right-2 text-5xl text-gray-50"></i>
        </div>
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Akurasi AI</p>
            <h3 class="text-3xl font-black {{ $accuracy >= 80 ? 'text-emerald-600' : 'text-orange-500' }}">
                {{ number_format($accuracy, 1) }}%
            </h3>
            <p class="text-[10px] text-gray-400 mt-1">Dari {{ $totalEvaluated }} data berlabel</p>
        </div>
        <div class="bg-green-50 p-5 rounded-2xl shadow-sm border border-green-100 text-center">
            <p class="text-xs font-bold text-green-800 uppercase mb-1">Positif</p>
            <h3 class="text-3xl font-black text-green-600">{{ number_format($totalPositif) }}</h3>
        </div>
        <div class="bg-gray-50 p-5 rounded-2xl shadow-sm border border-gray-200 text-center">
            <p class="text-xs font-bold text-gray-800 uppercase mb-1">Netral</p>
            <h3 class="text-3xl font-black text-gray-600">{{ number_format($totalNetral) }}</h3>
        </div>
        <div class="bg-red-50 p-5 rounded-2xl shadow-sm border border-red-100 text-center">
            <p class="text-xs font-bold text-red-800 uppercase mb-1">Negatif</p>
            <h3 class="text-3xl font-black text-red-600">{{ number_format($totalNegatif) }}</h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- GRAFIK TREN (LINE CHART) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 lg:col-span-2">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-arrow-trend-up text-blue-500"></i> Tren Sentimen Berdasarkan Waktu
            </h3>
            <div class="relative h-80 w-full">
                <canvas id="trendChart"></canvas>
            </div>
        </div>

        {{-- DIAGRAM KOMPOSISI (DOUGHNUT CHART) --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-chart-pie text-purple-500"></i> Komposisi Prediksi AI
            </h3>
            <div class="relative h-64 w-full flex items-center justify-center">
                <canvas id="compositionChart"></canvas>
            </div>
            <div class="mt-6 space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-emerald-500"></div> Positif
                    </span>
                    <span class="font-bold">{{ $totalData > 0 ? number_format(($totalPositif/$totalData)*100, 1) : 0 }}%</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-gray-400"></div> Netral
                    </span>
                    <span class="font-bold">{{ $totalData > 0 ? number_format(($totalNetral/$totalData)*100, 1) : 0 }}%</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div> Negatif
                    </span>
                    <span class="font-bold">{{ $totalData > 0 ? number_format(($totalNegatif/$totalData)*100, 1) : 0 }}%</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {

        // Deklarasi data aman dari PHP ke Javascript
        const chartLabels = @json($chartLabels);
        const trendPositifData = @json($trendPositif);
        const trendNegatifData = @json($trendNegatif);
        const trendNetralData = @json($trendNetral);

        const countPositif = @json($totalPositif);
        const countNetral = @json($totalNetral);
        const countNegatif = @json($totalNegatif);

        // 1. INIT GRAFIK TREN (LINE)
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                        label: 'Positif',
                        data: trendPositifData,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Negatif',
                        data: trendNegatifData,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Netral',
                        data: trendNetralData,
                        borderColor: '#9CA3AF',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 4]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        // 2. INIT GRAFIK KOMPOSISI (DOUGHNUT)
        const compCtx = document.getElementById('compositionChart').getContext('2d');
        new Chart(compCtx, {
            type: 'doughnut',
            data: {
                labels: ['Positif', 'Netral', 'Negatif'],
                datasets: [{
                    data: [countPositif, countNetral, countNegatif],
                    backgroundColor: ['#10B981', '#9CA3AF', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

    });
</script>
@endsection
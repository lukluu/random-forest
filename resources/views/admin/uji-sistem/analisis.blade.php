@extends('layouts.admin')

@section('content')
<div class="p-6">
    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#5D4037] flex items-center gap-2">
                <i class="fa-solid fa-chart-pie"></i> Analisis Akurasi Model
            </h2>
            <p class="text-gray-500 mt-1">Evaluasi kinerja AI dibandingkan dengan <b>Ground Truth</b> (Label Manual).</p>
        </div>
        <a href="{{ route('admin.uji-sistem') }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
            <i class="fa-solid fa-arrow-left mr-2"></i> Kembali ke Simulasi
        </a>
    </div>

    {{-- KARTU RINGKASAN --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Kartu 1: Akurasi --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between relative overflow-hidden">
            <div class="relative z-10">
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Akurasi Sistem</p>
                <h3 class="text-4xl font-black {{ $accuracy >= 80 ? 'text-green-600' : ($accuracy >= 60 ? 'text-yellow-600' : 'text-red-600') }}">
                    {{ number_format($accuracy, 1) }}%
                </h3>
                <p class="text-xs text-gray-400 mt-1">Persentase kecocokan prediksi</p>
            </div>
            <div class="w-16 h-16 rounded-full {{ $accuracy >= 80 ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' }} flex items-center justify-center text-2xl">
                <i class="fa-solid fa-bullseye"></i>
            </div>
        </div>

        {{-- Kartu 2: Total Data --}}
        <a href="{{ route('admin.uji-sistem.history') }}" class="block group">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between hover:shadow-md hover:border-blue-200 transition-all cursor-pointer">
                <div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-wider group-hover:text-blue-600 transition-colors">Total Sampel Uji</p>
                    <h3 class="text-4xl font-black text-[#5D4037]">{{ $totalData }}</h3>
                    <p class="text-xs text-gray-400 mt-1">Klik untuk lihat riwayat</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                    <i class="fa-solid fa-database"></i>
                </div>
            </div>
        </a>

        {{-- Kartu 3: Error Rate --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex items-center justify-between">
            <div>
                <p class="text-sm font-bold text-gray-400 uppercase tracking-wider">Salah Prediksi</p>
                <h3 class="text-4xl font-black text-red-500">{{ count($mismatches) }}</h3>
                <p class="text-xs text-gray-400 mt-1">Ketidakcocokan AI vs Manual</p>
            </div>
            <div class="w-16 h-16 rounded-full bg-red-100 text-red-600 flex items-center justify-center text-2xl">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">

        {{-- 1. CONFUSION MATRIX --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <h3 class="font-bold text-[#5D4037] text-lg mb-6">Confusion Matrix</h3>

            <div class="relative flex justify-center">
                {{-- Label Sumbu --}}
                <div class="absolute -left-4 top-1/2 -rotate-90 text-xs font-bold text-gray-400 tracking-widest">GROUND TRUTH</div>

                <div class="w-full pl-6">
                    <div class="text-center text-xs font-bold text-gray-400 tracking-widest mb-2">PREDIKSI AI</div>

                    {{-- Grid Matrix --}}
                    <div class="grid grid-cols-4 gap-2 text-center text-sm">
                        {{-- Header Kolom --}}
                        <div></div>
                        <div class="font-bold text-green-600 bg-gray-50 rounded py-1">Positif</div>
                        <div class="font-bold text-gray-500 bg-gray-50 rounded py-1">Netral</div>
                        <div class="font-bold text-red-500 bg-gray-50 rounded py-1">Negatif</div>

                        {{-- Baris Positif --}}
                        <div class="font-bold text-green-600 flex items-center justify-end pr-2">Positif</div>
                        <div class="bg-green-100 p-3 rounded text-green-800 font-black border border-green-200">
                            {{ $matrix['Positif']['Positif'] }}
                        </div>
                        <div class="bg-white p-3 rounded text-gray-400 border border-gray-100">
                            {{ $matrix['Positif']['Netral'] }}
                        </div>
                        <div class="bg-white p-3 rounded text-red-400 border border-gray-100">
                            {{ $matrix['Positif']['Negatif'] }}
                        </div>

                        {{-- Baris Netral --}}
                        <div class="font-bold text-gray-500 flex items-center justify-end pr-2">Netral</div>
                        <div class="bg-white p-3 rounded text-green-400 border border-gray-100">
                            {{ $matrix['Netral']['Positif'] }}
                        </div>
                        <div class="bg-gray-200 p-3 rounded text-gray-800 font-black border border-gray-300">
                            {{ $matrix['Netral']['Netral'] }}
                        </div>
                        <div class="bg-white p-3 rounded text-red-400 border border-gray-100">
                            {{ $matrix['Netral']['Negatif'] }}
                        </div>

                        {{-- Baris Negatif --}}
                        <div class="font-bold text-red-500 flex items-center justify-end pr-2">Negatif</div>
                        <div class="bg-white p-3 rounded text-green-400 border border-gray-100">
                            {{ $matrix['Negatif']['Positif'] }}
                        </div>
                        <div class="bg-white p-3 rounded text-gray-400 border border-gray-100">
                            {{ $matrix['Negatif']['Netral'] }}
                        </div>
                        <div class="bg-red-100 p-3 rounded text-red-800 font-black border border-red-200">
                            {{ $matrix['Negatif']['Negatif'] }}
                        </div>
                    </div>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-6 text-center italic">*Diagonal warna (kotak gelap) menunjukkan prediksi yang BENAR.</p>
        </div>

        {{-- 2. CHART VISUAL --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 flex flex-col items-center justify-center">
            <h3 class="font-bold text-[#5D4037] text-lg w-full text-left mb-2">Komposisi Prediksi</h3>
            <div id="chart-accuracy" class="w-full"></div>
        </div>
    </div>

    {{-- TABEL MISMATCH (YANG SALAH) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
            <h3 class="font-bold text-red-700">Daftar Kesalahan Prediksi (Mismatch)</h3>
            <span class="text-xs bg-white text-red-600 px-2 py-1 rounded border border-red-100">AI vs Manual Berbeda</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-500 uppercase font-bold text-xs">
                    <tr>
                        <th class="px-6 py-3">Ulasan</th>
                        <th class="px-6 py-3">Ground Truth</th>
                        <th class="px-6 py-3">Prediksi AI</th>
                        <th class="px-6 py-3">Confidence</th>
                        <th class="px-6 py-3">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($mismatches as $item)
                    {{--
                        LOGIC UPDATE:
                        Kita tidak lagi menghitung manual label dari score_average.
                        Kita ambil langsung dari kolom ground_truth di database.
                    --}}
                    @php
                    $manualLabel = $item->ground_truth ?? '-';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 max-w-xs truncate" title="{{ $item->review }}">
                            "{{ $item->review }}"
                        </td>

                        {{-- Ground Truth (Manual) --}}
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-bold 
                                {{ $manualLabel == 'Positif' ? 'bg-green-100 text-green-700' : ($manualLabel == 'Negatif' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                                {{ $manualLabel }}
                            </span>
                        </td>

                        {{-- Prediksi AI --}}
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 rounded text-xs font-bold border-2
                                {{ $item->sentiment == 'Positif' ? 'border-green-100 text-green-700' : ($item->sentiment == 'Negatif' ? 'border-red-100 text-red-700' : 'border-gray-100 text-gray-700') }}">
                                {{ $item->sentiment }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-gray-500">
                            {{ number_format($item->confidence_score, 1) }}%
                        </td>
                        <td class="px-6 py-4 text-gray-400 text-xs">
                            {{ $item->created_at->format('d M Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fa-solid fa-check-circle text-green-500 text-3xl mb-3 block"></i>
                            <p class="font-medium">Luar biasa! Semua prediksi AI sesuai dengan Ground Truth.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {

        // 1. AMBIL DATA JSON DARI CONTROLLER
        const chartData = @json($chartData ?? null);

        // Fallback jika data kosong
        if (!chartData || (chartData.series[0] === 0 && chartData.series[1] === 0)) {
            var dummySeries = [1];
            var dummyLabels = ['Belum ada data'];
            var dummyColors = ['#E5E7EB'];
            var dummyAcc = "0%";
            renderChart(dummySeries, dummyLabels, dummyColors, dummyAcc);
        } else {
            renderChart(chartData.series, chartData.labels, chartData.colors, chartData.accuracy);
        }

        function renderChart(series, labels, colors, accuracyText) {
            var options = {
                series: series,
                labels: labels,
                colors: colors,

                chart: {
                    type: 'donut',
                    height: 320,
                    fontFamily: 'Instrument Sans, sans-serif'
                },

                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '14px',
                                    fontWeight: 600,
                                    offsetY: -10
                                },
                                value: {
                                    show: true,
                                    fontSize: '16px',
                                    fontWeight: 400,
                                    offsetY: 16,
                                    formatter: function(val) {
                                        return val + " Data";
                                    }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'Akurasi',
                                    fontSize: '18px',
                                    fontWeight: 600,
                                    color: '#374151',
                                    formatter: function(w) {
                                        return accuracyText;
                                    }
                                }
                            }
                        }
                    }
                },

                dataLabels: {
                    enabled: false
                },
                legend: {
                    position: 'bottom',
                    itemMargin: {
                        horizontal: 10,
                        vertical: 5
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            return val + " Data"
                        }
                    }
                }
            };

            var chartElement = document.querySelector("#chart-accuracy");
            if (chartElement) {
                var chart = new ApexCharts(chartElement, options);
                chart.render();
            }
        }
    });
</script>
@endsection
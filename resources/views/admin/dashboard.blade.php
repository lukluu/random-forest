@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#5D4037]">Dashboard Overview</h2>
        <p class="text-gray-500 mt-1">Pantau performa kepuasan pelanggan secara real-time.</p>
    </div>

    <div class="flex items-center gap-2 text-sm text-gray-500 bg-white px-4 py-2 rounded-full border border-[#E6E0DC] shadow-sm">
        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
        Update Terakhir: {{ now()->format('d M Y, H:i') }}
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    {{-- KARTU 1: TOTAL RESPONDEN --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#E6E0DC] hover:border-[#8C5E3C] transition-colors group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium mb-1 uppercase tracking-wide">Total Responden</p>
                <h3 class="text-4xl font-bold text-[#5D4037]">{{ $stats['total_responden'] }}</h3>
            </div>
            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center shadow-sm group-hover:bg-blue-600 group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
        <div class="relative z-10 mt-4 flex items-center text-xs font-medium text-green-600 bg-green-50 w-fit px-2 py-1 rounded">
            <span>Data Realtime</span>
        </div>
    </div>

    {{-- KARTU 2: RATA-RATA SKOR --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#E6E0DC] hover:border-[#8C5E3C] transition-colors group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium mb-1 uppercase tracking-wide">Rata-rata Rating</p>
                <div class="flex items-baseline gap-1">
                    <h3 class="text-4xl font-bold text-[#5D4037]">{{ $stats['avg_score'] }}</h3>
                    <span class="text-sm text-gray-400 font-medium">/ 5.0</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center shadow-sm group-hover:bg-yellow-500 group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
            </div>
        </div>
        <div class="relative z-10 mt-4 w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
            <div class="bg-yellow-400 h-1.5 rounded-full" style="width: {{ ($stats['avg_score']/5)*100 }}%"></div>
        </div>
        <p class="relative z-10 mt-2 text-xs text-gray-400">
            @if($stats['avg_score'] >= 4) Kepuasan Sangat Baik @elseif($stats['avg_score'] >= 3) Cukup Baik @else Perlu Peningkatan @endif
        </p>
    </div>

    {{-- KARTU 3: SENTIMEN DOMINAN --}}
    <div class="bg-white p-6 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] border border-[#E6E0DC] hover:border-[#8C5E3C] transition-colors group relative overflow-hidden">
        <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition-transform"></div>
        <div class="relative z-10 flex justify-between items-start">
            <div>
                <p class="text-gray-500 text-sm font-medium mb-1 uppercase tracking-wide">Sentimen Model</p>
                <h3 class="text-4xl font-bold 
                    @if($stats['sentiment_dominant'] == 'Positif') text-green-600 
                    @elseif($stats['sentiment_dominant'] == 'Netral') text-gray-600 
                    @else text-red-600 @endif">
                    {{ $stats['sentiment_dominant'] }}
                </h3>
            </div>
            <div class="w-12 h-12 bg-green-50 text-green-600 rounded-xl flex items-center justify-center shadow-sm group-hover:bg-green-600 group-hover:text-white transition-all">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        <div class="relative z-10 mt-4 flex items-center text-xs text-gray-500">
            <span class="font-bold text-gray-700 mr-1">{{ $stats['sentiment_percentage'] }}%</span> responden {{ strtolower($stats['sentiment_dominant']) }}
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- CHART 1: ASPEK PENILAIAN --}}
    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-[#5D4037] text-lg">Performa Aspek Penilaian</h3>
        </div>
        <div id="chart-aspects"></div>
    </div>

    {{-- CHART 2: PIE CHART SENTIMEN --}}
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-[#E6E0DC]">
        <h3 class="font-bold text-[#5D4037] text-lg mb-6">Distribusi Sentimen</h3>
        <div id="chart-sentiment" class="flex justify-center"></div>

        <div class="mt-6 space-y-3">
            <div class="flex justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-green-500"></span> Positif</span>
                <span class="font-bold text-gray-700">{{ $pieChartPercentages['Positif'] }}%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-gray-400"></span> Netral</span>
                <span class="font-bold text-gray-700">{{ $pieChartPercentages['Netral'] }}%</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-red-500"></span> Negatif</span>
                <span class="font-bold text-gray-700">{{ $pieChartPercentages['Negatif'] }}%</span>
            </div>
        </div>
    </div>
</div>

{{-- TABEL DATA TERBARU --}}
<div class="bg-white rounded-2xl shadow-sm border border-[#E6E0DC] overflow-hidden">
    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-[#FDFBF9]">
        <h3 class="font-bold text-[#5D4037] text-lg">Ulasan Masuk Terbaru</h3>
        <a href="{{ route('data-survey') }}" class="text-sm font-medium text-[#8C5E3C] hover:text-[#5D4037] hover:underline transition-colors">Lihat Semua Data →</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-white text-gray-400 text-xs uppercase tracking-wider border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 font-semibold">Pelanggan</th>
                    <th class="px-6 py-4 font-semibold">Rating</th>
                    <th class="px-6 py-4 font-semibold">Review</th>
                    <th class="px-6 py-4 font-semibold">Sentimen Model</th>
                    <th class="px-6 py-4 font-semibold text-right">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-sm">
                @forelse($recentSurveys as $survey)
                <tr class="hover:bg-[#FDFBF9] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="font-bold text-[#5D4037]">{{ $survey['name'] }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center bg-yellow-50 w-fit px-2 py-1 rounded text-yellow-600 font-bold text-xs gap-1 border border-yellow-100">
                            {{ number_format($survey['score'], 1) }} <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 italic max-w-xs truncate group-hover:text-gray-700">
                        "{{ $survey['review'] }}"
                    </td>
                    <td class="px-6 py-4">
                        @if($survey['sentiment'] == 'Positif')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Positif
                        </span>
                        @elseif($survey['sentiment'] == 'Netral')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700 border border-gray-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-500 mr-1.5"></span> Netral
                        </span>
                        @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-1.5"></span> Negatif
                        </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-right font-mono text-xs">
                        {{ $survey['date'] }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada data survei masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const aspectLabels = @json($chartData['labels']);
    const aspectScores = @json($chartData['scores']);
    const pieData = @json($pieChartData); // Data dinamis dari Controller

    // 1. Bar Chart Config
    var optionsBar = {
        series: [{
            name: 'Skor Rata-rata',
            data: aspectScores
        }],
        chart: {
            type: 'bar',
            height: 320,
            fontFamily: 'Instrument Sans, sans-serif',
            toolbar: {
                show: false
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        colors: ['#8C5E3C'],
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '45%',
                distributed: true
            }
        },
        dataLabels: {
            enabled: false
        },
        legend: {
            show: false
        },
        xaxis: {
            categories: aspectLabels,
            labels: {
                style: {
                    colors: '#5D4037',
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            max: 5
        },
        grid: {
            borderColor: '#f3f3f3',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: function(val) {
                    return val + " / 5.0"
                }
            }
        },
        colors: ['#8C5E3C', '#70482D', '#A1887F', '#8D6E63', '#5D4037']
    };

    var chartBar = new ApexCharts(document.querySelector("#chart-aspects"), optionsBar);
    chartBar.render();

    // 2. Donut Chart Config (DINAMIS)
    var optionsPie = {
        series: pieData, // Menggunakan data real [Positif, Netral, Negatif]
        labels: ['Positif', 'Netral', 'Negatif'],
        chart: {
            type: 'donut',
            height: 300,
            fontFamily: 'Instrument Sans, sans-serif',
        },
        colors: ['#10B981', '#9CA3AF', '#EF4444'], // Hijau, Abu, Merah
        plotOptions: {
            pie: {
                donut: {
                    size: '70%',
                    labels: {
                        show: true,
                        total: {
                            show: true,
                            label: 'Total',
                            color: '#5D4037',
                            formatter: function(w) {
                                return w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b
                                }, 0)
                            }
                        }
                    }
                }
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: 0
        },
        legend: {
            show: false
        }
    };

    var chartPie = new ApexCharts(document.querySelector("#chart-sentiment"), optionsPie);
    chartPie.render();
</script>
@endsection
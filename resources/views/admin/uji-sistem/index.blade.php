@extends('layouts.admin')

@section('content')
<div class="p-6">

    {{-- WRAPPER UTAMA: Flex Row (Kiri: Form, Kanan: Hasil) --}}
    <div class="w-full flex flex-col lg:flex-row items-start gap-8">

        {{-- ================================================= --}}
        {{-- [KOLOM KIRI] : FORMULIR INPUT UTAMA               --}}
        {{-- ================================================= --}}
        <div class="flex-1 w-full min-w-0">

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER CARD: Judul di Kiri, Tombol Riwayat di Kanan --}}
                <div class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-flask text-coffee-accent"></i>
                            Simulasi Input
                        </h2>
                        <p class="text-sm text-gray-500">Masukkan parameter untuk menguji kecerdasan Model.</p>
                    </div>

                    {{-- TOMBOL LIHAT DATA (SESUAI REQUEST) --}}
                    <a href="{{ route('admin.uji-sistem.history') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                        <i class="fa-solid fa-list-ul"></i>
                        Lihat Riwayat Data
                    </a>
                </div>

                <div class="px-8 py-2">
                    <form action="{{ route('admin.uji-sistem.test') }}" method="POST" class="space-y-8">
                        @csrf

                        {{-- 1. SLIDER INPUT --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                1. Kriteria Penilaian (Visual)
                            </label>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                                @php
                                $criteria = [
                                ['id' => 'rasa', 'label' => 'Cita Rasa', 'icon' => 'fa-mug-hot'],
                                ['id' => 'harga', 'label' => 'Harga', 'icon' => 'fa-tag'],
                                ['id' => 'pelayanan', 'label' => 'Pelayanan', 'icon' => 'fa-bell-concierge'],
                                ['id' => 'kebersihan', 'label' => 'Kebersihan', 'icon' => 'fa-broom'],
                                ['id' => 'keramahan', 'label' => 'Keramahan', 'icon' => 'fa-users'],
                                ];
                                @endphp

                                @foreach ($criteria as $item)
                                <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-transparent hover:border-coffee-accent/20 transition-all">
                                    <div class="w-10 h-10 rounded-lg bg-white shadow-sm flex items-center justify-center text-coffee-accent text-lg">
                                        <i class="fa-solid {{ $item['icon'] }}"></i>
                                    </div>

                                    <div class="flex-1">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-gray-700">{{ $item['label'] }}</span>
                                            <span class="font-bold text-coffee-accent text-sm" id="val-{{ $item['id'] }}">3</span>
                                        </div>
                                        <input type="range" id="{{ $item['id'] }}" name="{{ $item['id'] }}" min="1" max="5" value="3" step="1"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-coffee-accent"
                                            oninput="document.getElementById('val-{{ $item['id'] }}').innerText = this.value">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- 2. TEXT AREA --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                2. Teks Ulasan (Wajib Diisi)
                            </label>
                            <div class="relative group">
                                <textarea name="ulasan_manual" id="ulasan_manual" rows="5" required
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-coffee-accent focus:ring-4 focus:ring-coffee-accent/10 transition-all placeholder-gray-400 text-gray-700 resize-none shadow-sm text-base"
                                    placeholder="Tulis ulasan simulasi di sini... (Contoh: Kopinya enak tapi pelayanannya agak lambat)">{{ $ulasan ?? '' }}</textarea>
                                <div class="absolute bottom-4 right-4 text-gray-400 text-xs bg-white px-2 py-1 rounded shadow-sm border border-gray-100">
                                    <i class="fa-solid fa-keyboard mr-1"></i> Teks
                                </div>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div class="pt-6 mt-4 border-t border-gray-100">
                            <button type="submit" class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-3 text-lg">
                                <i class="fa-solid fa-microchip animate-pulse"></i>
                                <span>Jalankan Analisis</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- ================================================= --}}
        {{-- [KOLOM KANAN] : PANEL HASIL (STICKY)              --}}
        {{-- ================================================= --}}
        <div class="w-full lg:w-[400px] shrink-0 sticky top-6">

            @if(isset($result))
            {{-- KARTU HASIL (JIKA SUDAH ADA DATA) --}}
            <div class="bg-white rounded-2xl shadow-xl border border-coffee-accent/30 overflow-hidden animate-fade-in-up relative">
                @if(isset($analisisKonsistensi) && $analisisKonsistensi != 'Normal')
                <div class="px-6 pt-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg text-left shadow-sm animate-pulse">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fa-solid fa-triangle-exclamation text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700 font-bold">
                                    Perhatian!
                                </p>
                                <p class="text-xs text-yellow-600 mt-1">
                                    {{ $analisisKonsistensi }}
                                    <br>
                                    <span class="opacity-75">(Skor: {{ number_format($avgScore, 1) }} vs AI: {{ $result['sentiment'] }})</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                {{-- Badge Status --}}
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-1.5 h-1.5 bg-green-600 rounded-full mr-1.5 animate-pulse"></span>
                        Selesai
                    </span>
                </div>

                <div class="p-8 text-center">
                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-6">Hasil Deteksi</h3>

                    {{-- Sentimen Icon --}}
                    <div class="mb-6">
                        @if($result['sentiment'] == 'Positif')
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-green-100 to-green-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                            <i class="fa-solid fa-face-smile text-5xl text-green-500"></i>
                        </div>
                        <h2 class="text-4xl font-black text-gray-800">POSITIF</h2>
                        <p class="text-green-600 font-medium mt-1">Pelanggan merasa puas</p>
                        @elseif($result['sentiment'] == 'Netral')
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-gray-100 to-gray-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                            <i class="fa-solid fa-face-meh text-5xl text-gray-400"></i>
                        </div>
                        <h2 class="text-4xl font-black text-gray-800">NETRAL</h2>
                        <p class="text-gray-500 font-medium mt-1">Respon pelanggan standar</p>
                        @else
                        <div class="w-24 h-24 mx-auto bg-gradient-to-br from-red-100 to-red-50 rounded-full flex items-center justify-center mb-4 shadow-inner">
                            <i class="fa-solid fa-face-frown text-5xl text-red-500"></i>
                        </div>
                        <h2 class="text-4xl font-black text-gray-800">NEGATIF</h2>
                        <p class="text-red-500 font-medium mt-1">Pelanggan kecewa</p>
                        @endif
                    </div>

                    {{-- Confidence --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs text-gray-500 font-semibold">Tingkat Keyakinan</span>
                            <span class="text-xl font-bold text-coffee-accent">{{ number_format($result['confidence'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-coffee-accent h-2 rounded-full transition-all duration-1000" style="width: {{ $result['confidence'] }}%"></div>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="space-y-3">
                        @foreach($result['probs'] as $label => $prob)
                        <div class="flex items-center justify-between text-sm">
                            <span class="capitalize text-gray-500 w-16 text-left">{{ $label }}</span>
                            <div class="flex-1 mx-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                @php
                                $color = match($label) {
                                'positif' => 'bg-green-500',
                                'negatif' => 'bg-red-500',
                                default => 'bg-gray-400'
                                };
                                @endphp
                                <div class="{{ $color }} h-full rounded-full" style="width: {{ $prob * 100 }}%"></div>
                            </div>
                            <span class="font-mono font-bold text-gray-700 w-10 text-right">{{ number_format($prob * 100, 0) }}%</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            {{-- STATE: KOSONG / MENUNGGU (PLACEHOLDER) --}}
            <div class="bg-white rounded-2xl shadow-sm border-2 border-dashed border-gray-300 p-8 h-full min-h-[400px] flex flex-col items-center justify-center text-center group hover:border-coffee-accent/50 transition-colors">

                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <i class="fa-solid fa-chart-simple text-4xl text-gray-300 group-hover:text-coffee-accent transition-colors"></i>
                </div>

                <h3 class="text-lg font-bold text-gray-700 mb-2">Menunggu Analisis</h3>
                <p class="text-sm text-gray-400 max-w-[200px] leading-relaxed">
                    Hasil deteksi sentimen dan skor keyakinan model akan muncul di sini setelah Anda menekan tombol jalankan.
                </p>

                <div class="mt-8 flex gap-2">
                    <span class="w-2 h-2 rounded-full bg-gray-200"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-200"></span>
                    <span class="w-2 h-2 rounded-full bg-gray-200"></span>
                </div>
            </div>
            @endif

        </div>

    </div>
</div>

<style>
    input[type=range]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        background: #8C5E3C;
        margin-top: -6px;
        box-shadow: 0 0 0 3px white, 0 2px 4px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    input[type=range]::-webkit-slider-runnable-track {
        width: 100%;
        height: 4px;
        background: #e5e7eb;
        border-radius: 2px;
    }

    .animate-fade-in-up {
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
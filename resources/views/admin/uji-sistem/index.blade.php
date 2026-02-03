@extends('layouts.admin')

@section('content')
<div class="p-6">

    {{-- =================================================================== --}}
    {{-- [BARU] ALERT JIKA KONEKSI API GAGAL                                --}}
    {{-- Ditampilkan hanya jika ada session 'error' dari Controller         --}}
    {{-- =================================================================== --}}
    @if(session('error'))
    <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-6 flex items-center gap-4 shadow-sm animate-fade-in-up relative overflow-hidden">
        {{-- Aksen Background --}}
        <div class="absolute -right-10 -top-10 opacity-10">
            <i class="fa-solid fa-triangle-exclamation text-9xl text-red-500"></i>
        </div>

        {{-- Ikon Utama --}}
        <div class="bg-red-100 rounded-full p-4 text-red-600 shrink-0 z-10">
            <i class="fa-solid fa-plug-circle-xmark text-3xl animate-pulse"></i>
        </div>

        {{-- Konten Teks --}}
        <div class="z-10">
            <h3 class="text-red-900 font-bold text-xl">Gagal Terhubung ke Model AI!</h3>
        </div>
    </div>
    @endif
    {{-- =================================================================== --}}


    {{-- WRAPPER UTAMA --}}
    <div class="w-full flex flex-col lg:flex-row items-start gap-8">

        {{-- [KOLOM KIRI] : FORMULIR INPUT --}}
        <div class="flex-1 w-full min-w-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                {{-- HEADER --}}
                <div class="px-8 py-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-flask text-coffee-accent"></i>
                            Simulasi & Validasi
                        </h2>
                        <p class="text-sm text-gray-500">Uji model AI dan bandingkan dengan label manual.</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.uji-sistem.analysis') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-sm font-medium rounded-lg transition-colors border border-indigo-100">
                            <i class="fa-solid fa-chart-pie"></i> Analisis
                        </a>
                        <a href="{{ route('admin.uji-sistem.history') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors border border-gray-200">
                            <i class="fa-solid fa-list-ul"></i> Riwayat
                        </a>
                    </div>
                </div>

                <div class="px-8 py-6">
                    <form action="{{ route('admin.uji-sistem.test') }}" method="POST" class="space-y-8">
                        @csrf

                        {{-- 1. SLIDER INPUT (VISUAL) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                1. Penilaian Visual (Slider)
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
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
                                <div class="flex items-center gap-3 bg-gray-50 p-2.5 rounded-xl border border-transparent hover:border-coffee-accent/20 transition-all">
                                    <div class="w-8 h-8 rounded-lg bg-white shadow-sm flex items-center justify-center text-coffee-accent">
                                        <i class="fa-solid {{ $item['icon'] }}"></i>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-bold text-gray-700">{{ $item['label'] }}</span>
                                            <span class="font-bold text-coffee-accent" id="val-{{ $item['id'] }}">3</span>
                                        </div>
                                        <input type="range" id="{{ $item['id'] }}" name="{{ $item['id'] }}" min="1" max="5" value="3" step="1"
                                            class="w-full h-1.5 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-coffee-accent"
                                            oninput="document.getElementById('val-{{ $item['id'] }}').innerText = this.value">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-gray-100">

                        {{-- 2. GROUND TRUTH (LABEL MANUAL) --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                2. Label Seharusnya (Ground Truth) <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-3 gap-4">
                                {{-- Pilihan Positif --}}
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="ground_truth" value="Positif" class="peer sr-only" required>
                                    <div class="p-3 text-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-green-50 peer-checked:bg-green-100 peer-checked:text-green-700 peer-checked:border-green-500 transition-all">
                                        <i class="fa-solid fa-face-smile text-2xl mb-1 block"></i>
                                        <span class="text-sm font-bold">Positif</span>
                                    </div>
                                </label>

                                {{-- Pilihan Netral --}}
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="ground_truth" value="Netral" class="peer sr-only">
                                    <div class="p-3 text-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-gray-50 peer-checked:bg-gray-200 peer-checked:text-gray-800 peer-checked:border-gray-500 transition-all">
                                        <i class="fa-solid fa-face-meh text-2xl mb-1 block"></i>
                                        <span class="text-sm font-bold">Netral</span>
                                    </div>
                                </label>

                                {{-- Pilihan Negatif --}}
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="ground_truth" value="Negatif" class="peer sr-only">
                                    <div class="p-3 text-center rounded-xl border border-gray-200 bg-white text-gray-500 hover:bg-red-50 peer-checked:bg-red-100 peer-checked:text-red-700 peer-checked:border-red-500 transition-all">
                                        <i class="fa-solid fa-face-frown text-2xl mb-1 block"></i>
                                        <span class="text-sm font-bold">Negatif</span>
                                    </div>
                                </label>
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Pilih sentimen yang <b>sebenarnya</b> dari ulasan di bawah ini untuk menguji akurasi AI.</p>
                        </div>

                        <hr class="border-gray-100">

                        {{-- 3. TEXT AREA --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-4">
                                3. Teks Ulasan
                            </label>
                            <div class="relative group">
                                <textarea name="ulasan_manual" id="ulasan_manual" rows="4" required
                                    class="w-full px-5 py-4 rounded-xl border border-gray-300 bg-gray-50 focus:bg-white focus:border-coffee-accent focus:ring-4 focus:ring-coffee-accent/10 transition-all placeholder-gray-400 text-gray-700 resize-none shadow-sm text-base"
                                    placeholder="Contoh: Tempatnya bagus tapi harganya agak mahal...">{{ $ulasan ?? '' }}</textarea>
                            </div>
                        </div>

                        {{-- TOMBOL SUBMIT --}}
                        <div>
                            {{-- Disable tombol jika ada error koneksi agar tidak di-spam --}}
                            <button type="submit" @if(session('error')) disabled @endif class="w-full bg-gray-800 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-3 text-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0">
                                <i class="fa-solid fa-microchip @if(!session('error')) animate-pulse @endif"></i>
                                <span>Proses Validasi</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- [KOLOM KANAN] : HASIL (STICKY) --}}
        <div class="w-full lg:w-[400px] shrink-0 sticky top-6">
            @if(isset($result) && !session('error'))
            <div class="bg-white rounded-2xl shadow-xl border border-coffee-accent/30 overflow-hidden animate-fade-in-up relative">

                {{-- INFO MATCHING --}}
                @if(isset($analisisKonsistensi))
                @if($analisisKonsistensi == 'Cocok')
                <div class="bg-green-100 p-4 text-center border-b border-green-200">
                    <div class="flex items-center justify-center gap-2 text-green-800 font-bold">
                        <i class="fa-solid fa-check-circle text-xl"></i>
                        <span>Model AI Benar!</span>
                    </div>
                    <p class="text-xs text-green-700 mt-1">Prediksi Model AI sesuai dengan Ground Truth.</p>
                </div>
                @else
                <div class="bg-red-100 p-4 text-center border-b border-red-200">
                    <div class="flex items-center justify-center gap-2 text-red-800 font-bold">
                        <i class="fa-solid fa-circle-xmark text-xl"></i>
                        <span>AI Salah!</span>
                    </div>
                    <p class="text-xs text-red-700 mt-1">
                        Manual: <b>{{ $groundTruthInput }}</b> vs AI: <b>{{ $result['sentiment'] }}</b>
                    </p>
                </div>
                @endif
                @endif

                <div class="p-8 text-center">
                    <h3 class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-6">Hasil Prediksi AI</h3>

                    {{-- Sentimen Icon --}}
                    <div class="mb-6">
                        @php
                        $sentimenColor = match($result['sentiment']) {
                        'Positif' => 'text-green-500 bg-green-50',
                        'Netral' => 'text-gray-500 bg-gray-100',
                        'Negatif' => 'text-red-500 bg-red-50',
                        default => 'text-gray-500 bg-gray-100'
                        };
                        $sentimenIcon = match($result['sentiment']) {
                        'Positif' => 'fa-face-smile',
                        'Netral' => 'fa-face-meh',
                        'Negatif' => 'fa-face-frown',
                        default => 'fa-question'
                        };
                        @endphp
                        <div class="w-24 h-24 mx-auto rounded-full flex items-center justify-center mb-4 shadow-inner {{ $sentimenColor }}">
                            <i class="fa-solid {{ $sentimenIcon }} text-5xl"></i>
                        </div>
                        <h2 class="text-4xl font-black text-gray-800 uppercase">{{ $result['sentiment'] }}</h2>
                    </div>

                    {{-- Confidence --}}
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 mb-6">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-xs text-gray-500 font-semibold">Tingkat Keyakinan</span>
                            <span class="text-xl font-bold text-coffee-accent">{{ number_format($result['confidence'], 1) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-coffee-accent h-2 rounded-full" style="width: {{ $result['confidence'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            @else
            {{-- STATE: MENUNGGU INPUT / ERROR --}}
            <div class="bg-white rounded-2xl shadow-sm border-2 border-dashed {{ session('error') ? 'border-red-300 bg-red-50' : 'border-gray-300' }} p-8 h-full min-h-[400px] flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mb-6 {{ session('error') ? 'bg-red-100 text-red-400' : 'bg-gray-50 text-gray-300' }}">
                    @if(session('error'))
                    <i class="fa-solid fa-triangle-exclamation text-4xl"></i>
                    @else
                    <i class="fa-solid fa-robot text-4xl"></i>
                    @endif
                </div>
                @if(session('error'))
                <h3 class="text-lg font-bold text-red-700 mb-2">Sistem Offline</h3>
                <p class="text-sm text-red-500">Perbaiki koneksi ke API terlebih dahulu.</p>
                @else
                <h3 class="text-lg font-bold text-gray-700 mb-2">Menunggu Input</h3>
                <p class="text-sm text-gray-400">Silakan isi formulir di samping untuk menguji sistem.</p>
                @endif
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
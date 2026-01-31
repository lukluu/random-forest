@extends('layouts.app')

@section('title', 'Pertanyaan Survey - Kopi Kita')

@section('content')
<div class="w-full max-w-2xl mx-auto py-8 px-4">

    <div class="mb-8">
        <a href="{{ route('survey.start') }}" class="inline-flex items-center text-coffee-dark hover:text-coffee-accent font-medium mb-6 transition-colors group">
            <div class="w-8 h-8 rounded-full bg-white flex items-center justify-center shadow-sm mr-3 group-hover:scale-110 transition-transform">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            Kembali
        </a>

        <div class="text-center space-y-2">
            <h1 class="text-3xl md:text-4xl font-bold text-coffee-primary tracking-tight">Survey Kepuasan</h1>
            <p class="text-coffee-dark/60">Bantu kami meningkatkan kualitas layanan Kopi Kita</p>
        </div>
    </div>

    <div class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50 overflow-hidden relative">

        <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-coffee-dark to-coffee-accent"></div>

        <div class="p-6 md:p-10">
            <form action="{{ route('survey.store') }}" method="POST" class="space-y-10">
                @csrf

                <div class="space-y-8">
                    <h2 class="font-bold text-xl text-coffee-dark flex items-center gap-2">
                        <svg class="w-6 h-6 text-coffee-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Berikan Penilaian Anda
                    </h2>

                    @php
                    $criteria = [
                    ['id' => 'rasa', 'label' => 'Cita Rasa Kopi', 'icon' => 'M18 8h1a4 4 0 0 1 0 8h-1M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z M6 1v3 M10 1v3 M14 1v3'],
                    ['id' => 'harga', 'label' => 'Kesesuaian Harga', 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['id' => 'pelayanan', 'label' => 'Kualitas Pelayanan', 'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['id' => 'kebersihan', 'label' => 'Kebersihan Tempat', 'icon' => 'M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z'],
                    ['id' => 'keramahan', 'label' => 'Keramahan Staf', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z'],
                    ];
                    @endphp

                    @foreach ($criteria as $item)
                    <div class="slider-group">
                        <div class="flex justify-between items-end mb-4">
                            <label class="flex items-center gap-3 font-semibold text-coffee-dark text-lg">
                                <div class="p-2 bg-coffee-light rounded-lg text-coffee-accent">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['icon'] }}"></path>
                                    </svg>
                                </div>
                                {{ $item['label'] }}
                            </label>
                            <div class="text-2xl font-bold text-coffee-accent bg-coffee-light px-3 py-1 rounded-lg min-w-[3rem] text-center" id="val-{{ $item['id'] }}">
                                {{ old($item['id'], 3) }}
                            </div>
                        </div>

                        <div class="relative w-full h-10 flex items-center group">
                            <input type="range" name="{{ $item['id'] }}" id="{{ $item['id'] }}" min="1" max="5" value="{{ old($item['id'], 3) }}" step="1"
                                class="absolute w-full h-full opacity-0 cursor-pointer z-20"
                                oninput="updateSlider('{{ $item['id'] }}', this.value)">

                            <div class="w-full h-3 bg-[#E6E0DC] rounded-full overflow-hidden relative z-0">
                                <div id="track-{{ $item['id'] }}" class="h-full bg-coffee-accent transition-all duration-100 ease-out" style="width: 50%"></div>
                            </div>

                            <div id="thumb-{{ $item['id'] }}"
                                class="absolute w-7 h-7 bg-white border-4 border-coffee-accent rounded-full shadow-md transition-all duration-100 ease-out z-10 pointer-events-none transform -translate-x-1/2 group-hover:scale-110"
                                style="left: 50%;">
                            </div>
                        </div>

                        <div class="flex justify-between text-xs font-medium text-gray-400 mt-1 px-1">
                            <span>Sangat Tidak Puas</span>
                            <span>Sangat Puas</span>
                        </div>

                        @error($item['id'])
                        <p class="text-red-500 text-sm mt-1 font-medium">* {{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>

                <hr class="border-dashed border-gray-300">

                <div class="space-y-3">
                    <label for="review" class="flex items-center gap-2 font-bold text-coffee-dark text-lg">
                        <svg class="w-5 h-5 text-coffee-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Review Singkat (Opsional)
                    </label>
                    <textarea name="review" id="review" rows="4"
                        class="w-full px-5 py-4 rounded-xl border border-[#E6E0DC] bg-[#FDFBF9] focus:outline-none focus:ring-2 focus:ring-coffee-accent focus:border-transparent transition-all placeholder-gray-400 text-gray-700 resize-none shadow-inner"
                        placeholder="Ceritakan pengalaman ngopi Anda di sini... (Contoh: Kopinya enak, tapi AC kurang dingin)">{{ old('review') }}</textarea>
                    <p class="text-xs text-gray-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Review akan dianalisis otomatis oleh AI untuk mendeteksi sentimen.
                    </p>
                </div>

                <button type="submit" class="w-full bg-coffee-accent hover:bg-coffee-accentHover text-white font-bold py-4 rounded-xl shadow-lg shadow-coffee-accent/20 transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 text-lg">
                    <span>Kirim Penilaian</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>

            </form>
        </div>
    </div>
</div>

<script>
    function updateSlider(id, value) {
        // 1. Update Angka
        document.getElementById('val-' + id).innerText = value;

        // 2. Hitung Persentase (1-5 scale)
        let percent = (value - 1) / (5 - 1) * 100;

        // 3. Update Visual
        document.getElementById('track-' + id).style.width = percent + '%';
        document.getElementById('thumb-' + id).style.left = percent + '%';
    }

    // Inisialisasi Slider saat halaman dimuat (Agar tidak kosong)
    document.addEventListener("DOMContentLoaded", function() {
        const sliders = ['rasa', 'harga', 'pelayanan', 'kebersihan', 'keramahan'];
        sliders.forEach(id => {
            let input = document.getElementById(id);
            if (input) {
                updateSlider(id, input.value);
            }
        });
    });
</script>
@endsection
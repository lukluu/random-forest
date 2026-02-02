@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="max-w-5xl mx-auto">

        {{-- Header Simple --}}
        <div class="mb-6 flex items-center gap-3">
            <div class="p-3 bg-white rounded-xl shadow-sm border border-gray-100">
                <i class="fa-solid fa-flask text-2xl text-coffee-accent"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Uji Kecerdasan AI</h1>
                <p class="text-sm text-gray-500">Simulasi input user untuk melihat prediksi sentimen.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            {{-- KOLOM KIRI: FORMULIR (Lebar 8/12) --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="p-6 md:p-8">

                        <form action="{{ route('admin.uji-sistem.process') }}" method="POST" class="space-y-8">
                            @csrf

                            {{-- BAGIAN 1: SLIDER (VISUAL SAJA) --}}
                            <div>
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                    1. Input Kriteria (Visual)
                                </h3>

                                <div class="space-y-5">
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
                                    <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                                        {{-- Label di Kiri --}}
                                        <label class="w-full sm:w-40 flex items-center gap-3 text-gray-700 font-medium text-sm shrink-0">
                                            <span class="w-6 text-center text-coffee-accent"><i class="fa-solid {{ $item['icon'] }}"></i></span>
                                            {{ $item['label'] }}
                                        </label>

                                        {{-- Slider di Tengah --}}
                                        <div class="flex-1 relative h-6 flex items-center group">
                                            <input type="range" id="{{ $item['id'] }}" min="1" max="5" value="3" step="1"
                                                class="absolute w-full h-full opacity-0 cursor-pointer z-20"
                                                oninput="updateSlider('{{ $item['id'] }}', this.value)">

                                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden relative z-0">
                                                <div id="track-{{ $item['id'] }}" class="h-full bg-coffee-accent transition-all duration-100" style="width: 50%"></div>
                                            </div>

                                            <div id="thumb-{{ $item['id'] }}" class="absolute w-4 h-4 bg-white border-2 border-coffee-accent rounded-full shadow transition-all duration-100 z-10 pointer-events-none transform -translate-x-1/2" style="left: 50%;"></div>
                                        </div>

                                        {{-- Angka di Kanan --}}
                                        <div class="font-bold text-coffee-accent text-sm w-6 text-right" id="val-{{ $item['id'] }}">3</div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- BAGIAN 2: REVIEW TEXT (WAJIB) --}}
                            <div>
                                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                                    2. Teks Ulasan (Diproses AI)
                                </h3>
                                <div class="relative">
                                    <textarea name="ulasan_manual" id="ulasan_manual" rows="3" required
                                        class="w-full pl-4 pr-4 py-3 rounded-xl border border-gray-300 focus:border-coffee-accent focus:ring-1 focus:ring-coffee-accent transition-all placeholder-gray-400 text-gray-700 resize-none"
                                        placeholder="Tulis ulasan simulasi di sini...">{{ $ulasan ?? '' }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-coffee-dark hover:bg-coffee-primary text-white font-semibold py-3 rounded-xl shadow-md transition-all flex items-center justify-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                                <span>Analisis AI</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: HASIL (Lebar 4/12) --}}
            <div class="lg:col-span-4">
                @if(isset($result))
                <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-6 animate-fade-in-up">
                    <div class="bg-coffee-dark p-4 text-center">
                        <h3 class="font-bold text-white text-sm uppercase tracking-wide">Hasil Prediksi</h3>
                    </div>

                    <div class="p-6 text-center">

                        {{-- Sentimen Besar --}}
                        <div class="mb-6">
                            @if($result['sentiment'] == 'Positif')
                            <i class="fa-solid fa-face-smile text-5xl text-green-500 mb-2 block"></i>
                            <span class="text-2xl font-black text-gray-800">POSITIF</span>
                            @elseif($result['sentiment'] == 'Netral')
                            <i class="fa-solid fa-face-meh text-5xl text-gray-400 mb-2 block"></i>
                            <span class="text-2xl font-black text-gray-800">NETRAL</span>
                            @else
                            <i class="fa-solid fa-face-frown text-5xl text-red-500 mb-2 block"></i>
                            <span class="text-2xl font-black text-gray-800">NEGATIF</span>
                            @endif
                        </div>

                        {{-- Confidence Bar --}}
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 mb-4 text-left">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-xs font-bold text-gray-500">Keyakinan AI</span>
                                <span class="text-sm font-bold text-coffee-accent">{{ number_format($result['confidence'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-coffee-accent h-2 rounded-full transition-all duration-1000" style="width: {{ $result['confidence'] }}%"></div>
                            </div>
                        </div>

                        {{-- Breakdown --}}
                        <div class="space-y-2 text-left">
                            <p class="text-xs font-bold text-gray-400 uppercase">Detail Probabilitas:</p>
                            @foreach($result['probs'] as $label => $prob)
                            <div class="flex items-center justify-between text-xs">
                                <span class="capitalize text-gray-600">{{ $label }}</span>
                                <span class="font-mono text-gray-500">{{ number_format($prob * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-100 rounded-full h-1">
                                @php
                                $color = match($label) {
                                'positif' => 'bg-green-500',
                                'negatif' => 'bg-red-500',
                                default => 'bg-gray-400'
                                };
                                @endphp
                                <div class="{{ $color }} h-1 rounded-full" style="width: {{ $prob * 100 }}%"></div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @else
                {{-- State Kosong --}}
                <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-6 text-center text-gray-400 h-64 flex flex-col items-center justify-center">
                    <i class="fa-solid fa-robot text-3xl mb-2 opacity-30"></i>
                    <p class="text-xs">Hasil analisis akan muncul di sini.</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    function updateSlider(id, value) {
        document.getElementById('val-' + id).innerText = value;
        let percent = (value - 1) / (5 - 1) * 100;
        document.getElementById('track-' + id).style.width = percent + '%';
        document.getElementById('thumb-' + id).style.left = percent + '%';
    }
</script>
@endsection
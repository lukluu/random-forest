@extends('layouts.admin')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-file-csv text-emerald-600"></i> Uji Analisis Dataset
            </h1>
            <p class="text-sm text-gray-500">Unggah file CSV berisi banyak ulasan untuk diuji sekaligus oleh Model AI.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- [BARU] Tombol Riwayat Dataset --}}
            <a href="{{ route('admin.uji-dataset.analisis') }}" class="px-4 py-2 bg-blue-50 border border-blue-100 text-blue-700 hover:bg-blue-100 rounded-lg transition-colors flex items-center gap-2 shadow-sm font-medium text-sm">
                <i class="fa-solid fa-chart-line"></i> Analisis Statistik
            </a>
            <a href="{{ route('admin.uji-dataset.riwayat') }}" class="px-4 py-2 bg-emerald-50 border border-emerald-100 text-emerald-700 hover:bg-emerald-100 rounded-lg transition-colors flex items-center gap-2 shadow-sm font-medium text-sm">
                <i class="fa-solid fa-list-ul"></i> Riwayat Dataset
            </a>
        </div>
    </div>

    {{-- ALERT ERROR --}}
    @if(session('error') || $errors->any())
    <div class="mb-8 bg-red-50 border border-red-200 rounded-2xl p-6 flex items-start gap-4 shadow-sm relative overflow-hidden">
        <div class="bg-red-100 rounded-full p-4 text-red-600 shrink-0 z-10">
            <i class="fa-solid fa-plug-circle-xmark text-2xl animate-pulse"></i>
        </div>
        <div class="z-10">
            <h3 class="text-red-900 font-bold text-lg mb-1">Terjadi Kesalahan!</h3>
            @if(session('error'))
            <p class="text-red-700 text-sm">{{ session('error') }}</p>
            @endif
            @if($errors->any())
            <ul class="list-disc list-inside text-red-700 text-sm mt-1 ml-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
    @endif

    {{-- KARTU UPLOAD --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('admin.uji-dataset') }}" method="POST" enctype="multipart/form-data" onsubmit="showLoading()">
            @csrf

            <div class="p-8">
                {{-- Box Upload File --}}
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-12 text-center hover:border-emerald-500 transition-colors bg-gray-50/50 relative group cursor-pointer mb-6">
                    <input type="file" name="dataset_file" id="dataset_file" accept=".csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="updateFileName()">

                    <i class="fa-solid fa-cloud-arrow-up text-6xl text-gray-300 mb-4 group-hover:text-emerald-500 transition-colors"></i>
                    <h3 class="text-lg font-bold text-gray-700 mb-2">Klik atau Drag File CSV di sini</h3>
                    <p class="text-sm text-gray-500">Maksimal ukuran file 50 MB.</p>

                    {{-- Tampilan Nama File Terpilih --}}
                    <div id="file-name-display" class="mt-6 hidden justify-center">
                        <div class="inline-flex items-center gap-3 px-4 py-2 bg-emerald-50 text-emerald-800 rounded-xl border border-emerald-200 shadow-sm">
                            <i class="fa-solid fa-file-csv text-xl"></i>
                            <span id="file-name-text" class="font-semibold max-w-[250px] truncate"></span>
                            <i class="fa-solid fa-circle-check text-emerald-500 ml-2"></i>
                        </div>
                    </div>
                </div>

                {{-- Panduan Format --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 p-6 rounded-xl border border-gray-100">
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-circle-info text-blue-500"></i> Syarat Format CSV
                        </h4>
                        <ul class="text-sm text-gray-600 space-y-1.5 list-disc list-inside">
                            <li>Baris pertama harus berisi <b class="text-gray-800">Header</b>.</li>
                            <li>Wajib memiliki kolom bernama <b class="text-gray-800">text</b> atau <b class="text-gray-800">ulasan</b>.</li>
                            <li>Gunakan pemisah koma (<code>,</code>).</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-bullseye text-orange-500"></i> Fitur Deteksi Akurasi
                        </h4>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            Jika CSV Anda memiliki kolom <b class="text-gray-800">stars/rating</b> (angka 1-5) atau <b class="text-gray-800">ground_truth</b>, sistem akan otomatis menghitung persentase keakuratan model AI.
                        </p>
                    </div>
                </div>
            </div>

            <div class="px-8 py-5 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <span class="text-xs text-gray-400" id="loading-text">Pastikan server AI (Flask) sudah menyala.</span>
                <button type="submit" id="btn-submit-dataset" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-md flex items-center gap-3 transform hover:-translate-y-0.5">
                    <i class="fa-solid fa-microchip"></i> <span>Mulai Uji Analisis Dataset</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateFileName() {
        const input = document.getElementById('dataset_file');
        const display = document.getElementById('file-name-display');
        const text = document.getElementById('file-name-text');

        if (input.files.length > 0) {
            text.textContent = input.files[0].name;
            display.classList.remove('hidden');
            display.classList.add('flex');
        } else {
            display.classList.add('hidden');
            display.classList.remove('flex');
        }
    }

    function showLoading() {
        const btn = document.getElementById('btn-submit-dataset');
        const loadingText = document.getElementById('loading-text');

        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span>Sedang Menganalisis...</span>';
        btn.classList.add('opacity-70', 'cursor-not-allowed', 'pointer-events-none');

        loadingText.innerHTML = '<span class="text-emerald-600 font-bold animate-pulse">Memproses seluruh baris dataset... Mohon tunggu.</span>';
    }
</script>
@endsection
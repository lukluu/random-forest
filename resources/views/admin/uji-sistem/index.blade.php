@extends('layouts.admin') {{-- Sesuaikan dengan layout admin Anda --}}

@section('content')
<div class="p-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800">Uji Sistem (Batch Testing)</h1>
        <p class="text-gray-500">Upload file Excel (.xlsx) berisi ulasan untuk menguji akurasi model AI secara massal.</p>
    </div>

    {{-- BAGIAN 1: FORM UPLOAD --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form action="{{ route('admin.uji-sistem.process') }}" method="POST" enctype="multipart/form-data" class="flex flex-col md:flex-row gap-4 items-end">
            @csrf
            <div class="w-full md:w-1/2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File Excel</label>
                <input type="file" name="file" required accept=".xlsx, .xls, .csv"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-coffee-accent/10 file:text-coffee-accent hover:file:bg-coffee-accent/20 cursor-pointer border rounded-lg">
                <p class="mt-1 text-xs text-gray-400">*Format: Kolom A berisi teks ulasan.</p>
            </div>

            <div class="w-full md:w-auto">
                <button type="submit" class="w-full px-6 py-2.5 bg-coffee-accent text-white font-medium rounded-lg hover:bg-coffee-dark transition-colors flex items-center justify-center gap-2">
                    <i class="fa-solid fa-rocket"></i> Mulai Pengujian
                </button>
            </div>
        </form>

        {{-- Contoh Format --}}
        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100 flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-blue-500 mt-1"></i>
            <div class="text-sm text-blue-800">
                <span class="font-bold">Tips:</span> Pastikan file Excel Anda memiliki header <strong>"Ulasan"</strong> di baris pertama kolom A. Data ulasan dimulai dari baris kedua.
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: HASIL PENGUJIAN (Hanya Muncul Jika Ada Data) --}}
    @if(isset($results))

    {{-- Kartu Statistik Ringkas --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-sm text-gray-500">Total Data</p>
            <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-green-200 shadow-sm bg-green-50/50">
            <p class="text-sm text-green-600">Positif</p>
            <p class="text-2xl font-bold text-green-700">{{ $stats['positif'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-red-200 shadow-sm bg-red-50/50">
            <p class="text-sm text-red-600">Negatif</p>
            <p class="text-2xl font-bold text-red-700">{{ $stats['negatif'] }}</p>
        </div>
        <div class="bg-white p-4 rounded-xl border border-blue-200 shadow-sm bg-blue-50/50">
            <p class="text-sm text-blue-600">Rata-rata Akurasi AI</p>
            <p class="text-2xl font-bold text-blue-700">{{ number_format($stats['avg_confidence'], 1) }}%</p>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Detail Hasil Prediksi</h3>
            <span class="text-xs px-2 py-1 bg-gray-100 rounded text-gray-500">Selesai diproses</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-600 text-sm uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-3 font-semibold w-10">#</th>
                        <th class="px-6 py-3 font-semibold">Teks Ulasan</th>
                        <th class="px-6 py-3 font-semibold text-center">Prediksi Sentimen</th>
                        <th class="px-6 py-3 font-semibold text-center">Confidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($results as $index => $res)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-6 py-4 text-sm text-gray-400">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 max-w-lg">{{ $res['ulasan'] }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($res['sentiment'] == 'Positif')
                            <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">Positif</span>
                            @elseif($res['sentiment'] == 'Netral')
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">Netral</span>
                            @elseif($res['sentiment'] == 'Negatif')
                            <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-semibold">Negatif</span>
                            @else
                            <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-semibold">Error</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-16 bg-gray-200 rounded-full h-1.5 dark:bg-gray-700">
                                    <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ $res['confidence'] }}%"></div>
                                </div>
                                <span class="text-xs font-medium text-gray-600">{{ number_format($res['confidence'], 0) }}%</span>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>
@endsection
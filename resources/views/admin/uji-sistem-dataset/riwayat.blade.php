@extends('layouts.admin')

@section('content')
<div class="p-6">

    {{-- HEADER --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-database text-emerald-600"></i> Riwayat Uji Dataset
            </h1>
            <p class="text-sm text-gray-500">Menampilkan data ulasan yang telah disimpan dari proses upload CSV sebelumnya.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">

            {{-- [BARU] TOMBOL HAPUS SEMUA DATA --}}
            @if($totalData > 0)
            <form action="{{ route('admin.uji-dataset.reset') }}" method="POST" id="form-reset-dataset">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmReset()" class="px-5 py-2.5 bg-red-50 border border-red-100 text-red-600 hover:bg-red-100 font-bold rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i> Hapus Semua Data
                </button>
            </form>
            @endif

            <a href="{{ route('admin.uji-dataset.index') }}" class="px-5 py-2.5 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-upload"></i> Upload CSV Baru
            </a>
        </div>
    </div>

    {{-- ALERT PESAN SUKSES / ERROR --}}
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-circle-check text-xl"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-center gap-3">
        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
    @endif

    {{-- KARTU RINGKASAN DATA BASE --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 text-center">
            <p class="text-xs text-gray-400 font-bold uppercase mb-1">Total Tersimpan</p>
            <h3 class="text-2xl font-black text-gray-800">{{ number_format($totalData) }}</h3>
        </div>
        <div class="bg-green-50 p-4 rounded-xl shadow-sm border border-green-100 text-center text-green-700">
            <p class="text-xs font-bold uppercase mb-1">Prediksi Positif</p>
            <h3 class="text-2xl font-black">{{ number_format($totalPositif) }}</h3>
        </div>
        <div class="bg-gray-50 p-4 rounded-xl shadow-sm border border-gray-200 text-center text-gray-700">
            <p class="text-xs font-bold uppercase mb-1">Prediksi Netral</p>
            <h3 class="text-2xl font-black">{{ number_format($totalNetral) }}</h3>
        </div>
        <div class="bg-red-50 p-4 rounded-xl shadow-sm border border-red-100 text-center text-red-700">
            <p class="text-xs font-bold uppercase mb-1">Prediksi Negatif</p>
            <h3 class="text-2xl font-black">{{ number_format($totalNegatif) }}</h3>
        </div>
    </div>

    {{-- TABEL DATABASE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-sm"><i class="fa-solid fa-table-list mr-2"></i>Tabel Dataset Database</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-white border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">ID</th>
                        <th class="px-6 py-4 w-1/3">Teks Ulasan</th>
                        <th class="px-6 py-4 text-center">Reviewer / Waktu</th>
                        <th class="px-6 py-4 text-center">Data Asli (Label)</th>
                        <th class="px-6 py-4 text-center">Prediksi AI</th>
                        <th class="px-6 py-4 text-center">Confidence</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-[#FDFBF9] transition-colors">
                        <td class="px-6 py-4 text-center text-gray-400 font-mono text-xs">#{{ $item->id }}</td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 text-sm leading-relaxed line-clamp-3" title="{{ $item->review_text }}">
                                "{{ $item->review_text }}"
                            </p>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="text-xs font-bold text-gray-700">{{ $item->reviewer_name ?? 'Anonim' }}</div>
                            <div class="text-[10px] text-gray-400">
                                @if($item->published_at)
                                {{ \Carbon\Carbon::parse($item->published_at)->translatedFormat('d M Y, H:i') }} WIB
                                @else
                                -
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($item->ground_truth == 'Positif')
                            <span class="px-2 py-1 bg-green-50 text-green-700 rounded border border-green-200 text-[10px] font-bold uppercase">Positif</span>
                            @elseif($item->ground_truth == 'Negatif')
                            <span class="px-2 py-1 bg-red-50 text-red-700 rounded border border-red-200 text-[10px] font-bold uppercase">Negatif</span>
                            @elseif($item->ground_truth == 'Netral')
                            <span class="px-2 py-1 bg-gray-50 text-gray-700 rounded border border-gray-200 text-[10px] font-bold uppercase">Netral</span>
                            @else
                            <span class="text-gray-300 italic text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                            $predColor = match($item->ai_sentiment) {
                            'Positif' => 'text-green-600',
                            'Negatif' => 'text-red-600',
                            default => 'text-gray-600'
                            };
                            @endphp
                            <div class="flex flex-col items-center justify-center">
                                <span class="uppercase font-black {{ $predColor }}">
                                    {{ $item->ai_sentiment }}
                                </span>

                                @if($item->ground_truth)
                                @if($item->ground_truth == $item->ai_sentiment)
                                <span class="text-[9px] text-green-600 font-bold mt-1 bg-green-100 px-2 rounded-full"><i class="fa-solid fa-check"></i> Cocok</span>
                                @else
                                <span class="text-[9px] text-red-600 font-bold mt-1 bg-red-100 px-2 rounded-full"><i class="fa-solid fa-xmark"></i> Meleset</span>
                                @endif
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-[#8C5E3C]">{{ number_format((float)$item->confidence_score, 1) }}%</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fa-solid fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">Belum ada data ulasan dataset yang tersimpan.</p>
                            <a href="{{ route('admin.uji-dataset.index') }}" class="text-emerald-600 text-sm hover:underline mt-2 inline-block">Upload CSV sekarang</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINASI LARAVEL BAWAAN --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>

{{-- SCRIPT KONFIRMASI SWEETALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmReset() {
        Swal.fire({
            title: 'Hapus Semua Data?',
            text: "Seluruh riwayat dataset ({{ $totalData }} data) akan dihapus permanen. Anda tidak dapat mengembalikannya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444', // Red
            cancelButtonColor: '#6b7280', // Gray
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-reset-dataset').submit();
            }
        });
    }
</script>
@endsection
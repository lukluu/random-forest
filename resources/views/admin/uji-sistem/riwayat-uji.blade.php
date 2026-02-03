@extends('layouts.admin')

@section('content')
<div class="p-6">

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-coffee-accent"></i>
                Riwayat Pengujian AI
            </h1>
            <p class="text-sm text-gray-500">Daftar semua simulasi input yang telah dilakukan oleh Admin.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- [BARU] TOMBOL RESET SEMUA --}}
            @if($riwayat->count() > 0)
            <form action="{{ route('admin.uji-sistem.reset') }}" method="POST" id="form-reset-all">
                @csrf
                @method('DELETE')
                <button type="button" onclick="confirmReset()" class="px-5 py-2.5 bg-red-50 border border-red-100 text-red-600 hover:bg-red-100 font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-trash-can"></i>
                    Reset Riwayat
                </button>
            </form>
            @endif

            {{-- Tombol Analisis --}}
            <a href="{{ route('admin.uji-sistem.analysis') }}" class="px-5 py-2.5 bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-chart-pie"></i>
                Analisis Akurasi
            </a>

            {{-- Tombol Kembali --}}
            <a href="{{ route('admin.uji-sistem') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-coffee-dark font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 w-1/4">Ulasan</th>
                        <th class="px-6 py-4 text-center">Ground Truth</th>
                        <th class="px-6 py-4 text-center">Prediksi AI</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th> {{-- Kolom Aksi Baru --}}
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-[#FDFBF9] transition-colors group">

                        {{-- Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-700">
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $item->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- Review Text --}}
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 line-clamp-2 italic" title="{{ $item->review }}">
                                "{{ $item->review }}"
                            </p>
                        </td>

                        {{-- Ground Truth --}}
                        <td class="px-6 py-4 text-center">
                            @php
                            $gtColor = match($item->ground_truth) {
                            'Positif' => 'bg-green-50 text-green-700 border-green-100',
                            'Netral' => 'bg-gray-50 text-gray-700 border-gray-100',
                            'Negatif' => 'bg-red-50 text-red-700 border-red-100',
                            default => 'bg-gray-50 text-gray-400 border-gray-100'
                            };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold border {{ $gtColor }}">
                                {{ $item->ground_truth ?? '-' }}
                            </span>
                        </td>

                        {{-- Hasil Prediksi AI --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                @php
                                $aiColor = match($item->sentiment) {
                                'Positif' => 'text-green-600',
                                'Netral' => 'text-gray-600',
                                'Negatif' => 'text-red-600',
                                default => 'text-gray-400'
                                };
                                @endphp
                                <span class="text-sm font-black {{ $aiColor }} uppercase">
                                    {{ $item->sentiment }}
                                </span>
                                <span class="text-[10px] text-gray-400">
                                    {{ number_format((float)$item->confidence_score, 1) }}% Conf
                                </span>
                            </div>
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 text-center">
                            @if($item->ground_truth == $item->sentiment)
                            <div class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                                <i class="fa-solid fa-check"></i> Benar
                            </div>
                            @else
                            <div class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold">
                                <i class="fa-solid fa-xmark"></i> Salah
                            </div>
                            @endif
                        </td>

                        {{-- [BARU] KOLOM AKSI --}}
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2 opacity-1 group-hover:opacity-100 transition-opacity">
                                {{-- Tombol Detail --}}
                                <a href="{{ route('admin.uji-sistem.show', $item->id) }}" class="p-2 bg-white border border-gray-200 text-gray-500 rounded-lg hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm" title="Lihat Detail">
                                    <i class="fa-solid fa-eye"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('admin.uji-sistem.delete', $item->id) }}" method="POST" class="inline-block" id="del-{{ $item->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $item->id }}')" class="p-2 bg-white border border-gray-200 text-gray-500 rounded-lg hover:text-red-600 hover:border-red-200 transition-colors shadow-sm" title="Hapus Data">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center justify-center">
                                <i class="fa-solid fa-box-open text-4xl mb-3 opacity-30"></i>
                                <p>Belum ada riwayat pengujian.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
            {{ $riwayat->links() }}
        </div>
    </div>
</div>

{{-- SCRIPT SWEETALERT --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Konfirmasi Hapus Satu Baris
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus data ini?',
            text: "Data pengujian ini akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('del-' + id).submit();
            }
        })
    }

    // Konfirmasi Reset Semua
    function confirmReset() {
        Swal.fire({
            title: 'HAPUS SEMUA RIWAYAT?',
            text: "Tindakan ini akan mengosongkan seluruh tabel pengujian! Data tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Kosongkan Semua',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('form-reset-all').submit();
            }
        })
    }

    // Notifikasi Sukses
    @if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        timer: 3000,
        showConfirmButton: false
    });
    @endif
</script>
@endsection
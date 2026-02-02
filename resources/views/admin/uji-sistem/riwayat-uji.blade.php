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

        <a href="{{ route('admin.uji-sistem') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 hover:text-coffee-dark font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali ke Uji Sistem
        </a>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-semibold">
                        <th class="px-6 py-4">Tanggal & Waktu</th>
                        <th class="px-6 py-4">Admin</th>
                        <th class="px-6 py-4 w-1/3">Input Ulasan</th>
                        <th class="px-6 py-4 text-center">Skor Rata2</th>
                        <th class="px-6 py-4 text-center">Hasil AI</th>
                        <th class="px-6 py-4 text-center">Akurasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($riwayat as $item)
                    <tr class="hover:bg-[#FDFBF9] transition-colors">
                        {{-- Tanggal --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-700">
                                {{ $item->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-400">
                                {{ $item->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- Admin --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-coffee-light text-coffee-accent flex items-center justify-center text-xs font-bold">
                                    {{ substr($item->user->name ?? 'A', 0, 1) }}
                                </div>
                                <span class="text-sm text-gray-600">{{ $item->user->name ?? 'Admin' }}</span>
                            </div>
                        </td>

                        {{-- Review Text --}}
                        <td class="px-6 py-4">
                            <p class="text-sm text-gray-600 line-clamp-2 italic">
                                "{{ $item->review }}"
                            </p>
                        </td>

                        {{-- Skor Manual --}}
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-sm font-bold bg-gray-100 text-gray-600">
                                <i class="fa-solid fa-star text-yellow-400 text-xs mr-1"></i>
                                {{ $item->score_average }}
                            </span>
                        </td>

                        {{-- Hasil Sentimen --}}
                        <td class="px-6 py-4 text-center">
                            @if($item->sentiment == 'Positif')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                POSITIF
                            </span>
                            @elseif($item->sentiment == 'Netral')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                NETRAL
                            </span>
                            @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 border border-red-200">
                                NEGATIF
                            </span>
                            @endif
                        </td>

                        {{-- Confidence Score --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col items-center">
                                <span class="text-sm font-bold text-coffee-accent">
                                    {{ number_format((float)$item->confidence_score, 1) }}%
                                </span>
                                {{-- Mini Bar --}}
                                <div class="w-16 h-1 bg-gray-200 rounded-full mt-1">
                                    <div class="h-1 bg-coffee-accent rounded-full" style="width: {{ $item->confidence_score }}%"></div>
                                </div>
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
@endsection
@extends('layouts.admin')

@section('title', 'Data Survey Masuk')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#5D4037]">Data Survey Pelanggan</h2>
        <p class="text-gray-500 mt-1">Kelola dan pantau seluruh masukan yang masuk.</p>
    </div>

    <div class="flex gap-2">
        <div class="relative">
            <input type="text" placeholder="Cari nama..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#8C5E3C] focus:border-transparent">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <button class="px-4 py-2 bg-[#8C5E3C] text-white text-sm font-medium rounded-lg hover:bg-[#70482D] transition-colors flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Export Excel
        </button>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-[#E6E0DC] overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-[#FDFBF9] border-b border-[#E6E0DC]">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Pelanggan</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Rata-rata Skor</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Sentimen</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Review</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 text-sm">
                @foreach($surveys as $item)
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono text-xs">
                        {{ $item['date'] }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-bold text-[#5D4037]">{{ $item['name'] }}</div>
                        <div class="text-xs text-gray-400">{{ $item['email'] }}</div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center px-2.5 py-1 rounded-lg border 
                                {{ $item['avg_score'] >= 4 ? 'bg-green-50 border-green-100 text-green-700' : ($item['avg_score'] >= 3 ? 'bg-yellow-50 border-yellow-100 text-yellow-700' : 'bg-red-50 border-red-100 text-red-700') }}">
                            <span class="font-bold mr-1">{{ $item['avg_score'] }}</span>
                            <svg class="w-3 h-3 mb-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        @if($item['sentiment'] == 'Positif')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Positif
                        </span>
                        @elseif($item['sentiment'] == 'Netral')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Netral
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Negatif
                        </span>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <p class="text-gray-600 italic truncate max-w-xs group-hover:text-gray-900 transition-colors">
                            "{{ $item['review'] }}"
                        </p>
                    </td>

                    <td class="px-6 py-4 text-right">
                        <button class="text-gray-400 hover:text-[#8C5E3C] transition-colors p-1 rounded-md hover:bg-[#F8F5F2]" title="Lihat Detail">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-[#FDFBF9]">
        <span class="text-xs text-gray-500">Menampilkan 1-5 dari 128 data</span>
        <div class="flex gap-1">
            <button class="px-3 py-1 text-xs border rounded hover:bg-gray-50 disabled:opacity-50" disabled>&larr; Prev</button>
            <button class="px-3 py-1 text-xs border bg-[#8C5E3C] text-white rounded">1</button>
            <button class="px-3 py-1 text-xs border rounded hover:bg-gray-50">2</button>
            <button class="px-3 py-1 text-xs border rounded hover:bg-gray-50">3</button>
            <button class="px-3 py-1 text-xs border rounded hover:bg-gray-50">Next &rarr;</button>
        </div>
    </div>
</div>
@endsection
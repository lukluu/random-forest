@forelse($surveys as $item)
<tr class="hover:bg-gray-50 transition-colors group">
    {{-- TANGGAL --}}
    <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-mono text-xs">
        {{ $item->created_at->translatedFormat('d M Y H:i') }}
    </td>

    {{-- PELANGGAN --}}
    <td class="px-6 py-4">
        <div class="font-bold text-[#5D4037]">{{ $item->name }}</div>
        <div class="text-xs text-gray-400">{{ $item->email }}</div>
    </td>

    {{-- SKOR --}}
    <td class="px-6 py-4 text-center">
        <div class="inline-flex items-center px-2.5 py-1 rounded-lg border 
                {{ $item->score_average >= 4 ? 'bg-green-50 border-green-100 text-green-700' : ($item->score_average >= 3 ? 'bg-yellow-50 border-yellow-100 text-yellow-700' : 'bg-red-50 border-red-100 text-red-700') }}">
            <span class="font-bold mr-1">{{ number_format($item->score_average, 1) }}</span>
            <svg class="w-3 h-3 mb-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
        </div>
    </td>

    {{-- SENTIMEN --}}
    <td class="px-6 py-4">
        @if($item->sentiment == 'Positif')
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
            <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span> Positif
        </span>
        @elseif($item->sentiment == 'Netral')
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
            <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span> Netral
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
            <span class="w-1.5 h-1.5 rounded-full bg-red-600"></span> Negatif
        </span>
        @endif
    </td>

    {{-- REVIEW --}}
    <td class="px-6 py-4">
        <p class="text-gray-600 italic truncate max-w-xs group-hover:text-gray-900 transition-colors" title="{{ $item->review }}">
            "{{ $item->review }}"
        </p>
    </td>

    {{-- AKSI --}}
    <td class="px-6 py-4 text-right">
        {{-- Ubah BUTTON menjadi A (Link) --}}
        <a href="{{ route('data-survey.show', $item->id) }}"
            class="inline-block text-gray-400 hover:text-[#8C5E3C] transition-colors p-1 rounded-md hover:bg-[#F8F5F2]"
            title="Lihat Detail">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span>Detail</span>
        </a>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
        <div class="flex flex-col items-center justify-center">
            <p>Data tidak ditemukan.</p>
        </div>
    </td>
</tr>
@endforelse

{{-- PAGINATION (Penting agar pagination ikut ke-render saat search) --}}
<tr>
    <td colspan="6" class="px-6 py-4 border-t border-gray-100 bg-[#FDFBF9]">
        {{ $surveys->links() }}
    </td>
</tr>
@extends('layouts.admin')

@section('content')
<div class="p-6">
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Hasil Uji Analisis Dataset</h1>
            <p class="text-sm text-gray-500">Menganalisis <b>{{ $count }}</b> baris ulasan dari seluruh isi file CSV.</p>
        </div>
        <a href="{{ route('admin.uji-sistem') }}" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium rounded-xl transition-all shadow-sm flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Form
        </a>
    </div>

    @if($failedApiCount > 0)
    <div class="mb-6 bg-yellow-50 p-4 border border-yellow-200 text-yellow-800 rounded-lg flex items-center gap-3 shadow-sm">
        <i class="fa-solid fa-triangle-exclamation text-xl"></i>
        <p class="text-sm">Terdapat <b>{{ $failedApiCount }} baris</b> gagal diproses karena API Model tidak merespon (timeout).</p>
    </div>
    @endif

    {{-- KARTU STATISTIK --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Total --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Sukses Diuji</p>
            <h3 class="text-4xl font-black text-gray-800">{{ $count }}</h3>
            <i class="fa-solid fa-database absolute -bottom-4 -right-4 text-6xl text-gray-100"></i>
        </div>

        {{-- Akurasi --}}
        @if($totalEvaluated > 0)
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 relative overflow-hidden">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akurasi Model</p>
            <h3 class="text-4xl font-black {{ $accuracy >= 80 ? 'text-green-600' : 'text-yellow-600' }}">
                {{ number_format($accuracy, 1) }}%
            </h3>
            <p class="text-[10px] mt-1 text-gray-400">Dari {{ $totalEvaluated }} data berlabel</p>
        </div>
        @else
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Akurasi Model</p>
            <h3 class="text-2xl font-black text-gray-400 mt-2">Tidak Ada Label</h3>
            <p class="text-[10px] mt-1 text-gray-400">CSV tidak memiliki kolom rating/stars</p>
        </div>
        @endif

        {{-- Distribusi --}}
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-200 md:col-span-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Distribusi Prediksi Model</p>
            <div class="flex gap-4">
                <div class="flex-1 bg-green-50 text-green-700 p-3 rounded-lg text-center border border-green-100">
                    <span class="block text-2xl font-black">{{ $stats['Positif'] }}</span>
                    <span class="text-xs uppercase font-bold">Positif</span>
                </div>
                <div class="flex-1 bg-gray-50 text-gray-700 p-3 rounded-lg text-center border border-gray-100">
                    <span class="block text-2xl font-black">{{ $stats['Netral'] }}</span>
                    <span class="text-xs uppercase font-bold">Netral</span>
                </div>
                <div class="flex-1 bg-red-50 text-red-700 p-3 rounded-lg text-center border border-red-100">
                    <span class="block text-2xl font-black">{{ $stats['Negatif'] }}</span>
                    <span class="text-xs uppercase font-bold">Negatif</span>
                </div>
            </div>
        </div>
    </div>

    {{-- TABEL DATA BERHALAMAN (PAGINATED VIA JAVASCRIPT) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col">
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Rincian Hasil Prediksi Dataset</h3>

            {{-- Dropdown Pilihan Baris per Halaman --}}
            <select id="rowsPerPage" onchange="changeRowsPerPage()" class="text-sm border-gray-300 rounded-lg shadow-sm focus:border-coffee-accent focus:ring focus:ring-coffee-accent/20">
                <option value="15">15 Baris / Hal</option>
                <option value="50">50 Baris / Hal</option>
                <option value="100">100 Baris / Hal</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-white border-b border-gray-200 text-xs uppercase text-gray-500 font-bold">
                    <tr>
                        <th class="px-6 py-4 w-12 text-center">No</th>
                        <th class="px-6 py-4 w-1/2">Teks Ulasan</th>
                        <th class="px-6 py-4 text-center">Data Asli (Label)</th>
                        <th class="px-6 py-4 text-center">Prediksi Model</th>
                        <th class="px-6 py-4 text-center">Confidence</th>
                    </tr>
                </thead>
                {{-- Body Tabel Kosong, akan diisi oleh JavaScript --}}
                <tbody id="table-body" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>

        {{-- KONTROL PAGINASI --}}
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
            <span class="text-sm text-gray-600" id="page-info">Menampilkan data...</span>
            <div class="flex items-center gap-2">
                <button onclick="prevPage()" id="btn-prev" class="px-4 py-2 border border-gray-200 bg-white rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fa-solid fa-chevron-left"></i> Prev
                </button>
                <div id="page-numbers" class="flex gap-1">
                    {{-- Tombol Nomor Halaman akan digenerate disini --}}
                </div>
                <button onclick="nextPage()" id="btn-next" class="px-4 py-2 border border-gray-200 bg-white rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed">
                    Next <i class="fa-solid fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPT PAGINASI JAVASCRIPT --}}
<script>
    // 1. Ambil data JSON utuh dari Controller
    const dataset = @json($results);

    // 2. Variabel Paginasi
    let currentPage = 1;
    let rowsPerPage = 15;
    let totalPages = Math.ceil(dataset.length / rowsPerPage);

    // 3. Render Tabel Berdasarkan Halaman
    function renderTable() {
        const tbody = document.getElementById('table-body');
        tbody.innerHTML = ''; // Bersihkan tabel

        // Cek jika dataset kosong
        if (dataset.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="text-center py-8 text-gray-500">Tidak ada data hasil prediksi.</td></tr>`;
            updatePaginationUI();
            return;
        }

        let start = (currentPage - 1) * rowsPerPage;
        let end = start + rowsPerPage;
        let paginatedItems = dataset.slice(start, end);

        paginatedItems.forEach((item, index) => {
            let actualIndex = start + index + 1;

            // Logika Label Ground Truth
            let gtBadge = '';
            if (item.ground_truth === 'Positif') gtBadge = '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-lg font-bold text-[11px] border border-green-200 uppercase">Positif</span>';
            else if (item.ground_truth === 'Negatif') gtBadge = '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg font-bold text-[11px] border border-red-200 uppercase">Negatif</span>';
            else if (item.ground_truth === 'Netral') gtBadge = '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg font-bold text-[11px] border border-gray-200 uppercase">Netral</span>';
            else gtBadge = '<span class="text-gray-300 italic text-xs">Kosong</span>';

            // Logika Warna Prediksi Model
            let predColor = item.prediction === 'Positif' ? 'text-green-600' : (item.prediction === 'Negatif' ? 'text-red-600' : 'text-gray-600');

            // Logika Ikon Cocok/Meleset
            let iconIndicator = '';
            if (item.ground_truth !== '-') {
                if (item.ground_truth === item.prediction) {
                    iconIndicator = `<span class="text-[10px] text-green-600 font-bold mt-1 bg-green-100 border border-green-200 px-2 rounded-full"><i class="fa-solid fa-check"></i> Cocok</span>`;
                } else {
                    iconIndicator = `<span class="text-[10px] text-red-600 font-bold mt-1 bg-red-100 border border-red-200 px-2 rounded-full"><i class="fa-solid fa-xmark"></i> Meleset</span>`;
                }
            }

            // Susun Baris HTML
            let tr = `
                <tr class="hover:bg-blue-50/50 transition-colors">
                    <td class="px-6 py-4 text-center text-gray-400 font-mono">${actualIndex}</td>
                    <td class="px-6 py-4"><p class="text-gray-700 text-sm leading-relaxed">${item.text}</p></td>
                    <td class="px-6 py-4 text-center">${gtBadge}</td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <span class="uppercase font-black ${predColor}">${item.prediction}</span>
                            ${iconIndicator}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-bold text-[#8C5E3C]">${parseFloat(item.confidence).toFixed(1)}%</span>
                    </td>
                </tr>
            `;
            tbody.innerHTML += tr;
        });

        updatePaginationUI();
    }

    // 4. Update Tombol & Info
    function updatePaginationUI() {
        const info = document.getElementById('page-info');
        const btnPrev = document.getElementById('btn-prev');
        const btnNext = document.getElementById('btn-next');
        const pageNumbers = document.getElementById('page-numbers');

        if (dataset.length === 0) {
            info.innerHTML = "Menampilkan 0 data";
            btnPrev.disabled = true;
            btnNext.disabled = true;
            return;
        }

        let startText = ((currentPage - 1) * rowsPerPage) + 1;
        let endText = Math.min(currentPage * rowsPerPage, dataset.length);

        info.innerHTML = `Menampilkan <b>${startText}</b> sampai <b>${endText}</b> dari total <b>${dataset.length}</b> data`;

        btnPrev.disabled = currentPage === 1;
        btnNext.disabled = currentPage === totalPages;

        // Render Angka Halaman (Max 5 kotak biar rapi)
        pageNumbers.innerHTML = '';
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, currentPage + 2);

        for (let i = startPage; i <= endPage; i++) {
            let activeClass = i === currentPage ?
                'bg-emerald-600 text-white border-emerald-600' :
                'bg-white text-gray-600 border-gray-200 hover:bg-gray-100';

            pageNumbers.innerHTML += `
                <button onclick="goToPage(${i})" class="w-9 h-9 flex items-center justify-center border rounded-lg text-sm font-medium transition-colors ${activeClass}">
                    ${i}
                </button>
            `;
        }
    }

    // 5. Navigasi Fungsi
    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            renderTable();
        }
    }

    function nextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            renderTable();
        }
    }

    function goToPage(page) {
        currentPage = page;
        renderTable();
    }

    function changeRowsPerPage() {
        const select = document.getElementById('rowsPerPage');
        rowsPerPage = parseInt(select.value);
        totalPages = Math.ceil(dataset.length / rowsPerPage);
        currentPage = 1; // Reset ke halaman 1
        renderTable();
    }

    // 6. Eksekusi Pertama Kali Halaman Dimuat
    renderTable();
</script>
@endsection
@extends('layouts.admin')

@section('title', 'Data Survey Masuk')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
    <div>
        <h2 class="text-3xl font-bold text-[#5D4037]">Data Survey Pelanggan</h2>
        <p class="text-gray-500 mt-1">Kelola dan pantau seluruh masukan yang masuk.</p>
    </div>

    <div class="flex gap-2">
        {{-- INPUT SEARCH (Tambahkan ID="search-input") --}}
        <div class="relative">
            <input type="text"
                id="search-input"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama atau review..."
                class="pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#8C5E3C] focus:border-transparent w-64">

            {{-- Loading Indicator (Hidden by default) --}}
            <svg id="loading-icon" class="hidden w-4 h-4 text-[#8C5E3C] absolute right-3 top-3 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            <svg id="search-icon" class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
        <div class="flex gap-2">
            {{-- Form Reset Data --}}
            {{-- Perhatikan ID="form-reset" --}}
            <form id="form-reset" action="{{ route('data-survey.reset') }}" method="POST">
                @csrf
                @method('DELETE')

                {{-- Ubah type="button" agar tidak submit otomatis, kita handle via JS --}}
                <button type="button" id="btn-reset" class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Reset Data
                </button>
            </form>

            {{-- Tombol Export Excel (Tetap) --}}
            <a href="{{ route('data-survey.export', ['search' => request('search')]) }}"
                class="px-4 py-2 bg-[#107C41] text-white text-sm font-medium rounded-lg hover:bg-[#0b5c30] transition-colors flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z" />
                </svg>
                Export Excel
            </a>
        </div>
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

            {{-- BODY TABEL (Tambahkan ID="table-body") --}}
            <tbody id="table-body" class="divide-y divide-gray-100 text-sm">
                {{-- Include Partial View untuk load awal --}}
                @include('admin.ulasan.table')
            </tbody>
        </table>
    </div>

    {{-- Pagination dihapus dari sini karena sudah dipindah ke dalam partial survey_rows --}}
</div>

{{-- SCRIPT LIVE SEARCH (AJAX) --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        let timer;

        // Saat mengetik di input search
        $('#search-input').on('keyup', function() {
            clearTimeout(timer); // Hapus timer sebelumnya (Debounce)

            let query = $(this).val();
            let url = "{{ route('data-survey') }}";

            // Tampilkan icon loading, sembunyikan icon search
            $('#loading-icon').removeClass('hidden');
            $('#search-icon').addClass('hidden');

            // Tunggu 500ms setelah user selesai mengetik (Debouncing)
            timer = setTimeout(function() {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: {
                        search: query
                    },
                    success: function(data) {
                        // Ganti isi tbody dengan hasil baru dari server
                        $('#table-body').html(data);

                        // Sembunyikan loading
                        $('#loading-icon').addClass('hidden');
                        $('#search-icon').removeClass('hidden');
                    },
                    error: function() {
                        alert('Terjadi kesalahan koneksi.');
                        $('#loading-icon').addClass('hidden');
                    }
                });
            }, 500); // Waktu jeda 500ms
        });

        // Optional: Agar pagination juga jalan tanpa reload (AJAX Pagination)
        $(document).on('click', '.pagination a', function(event) {
            event.preventDefault();
            let pageUrl = $(this).attr('href');
            let query = $('#search-input').val(); // Ambil kata kunci saat ini

            $('#loading-icon').removeClass('hidden');

            $.ajax({
                url: pageUrl,
                data: {
                    search: query
                }, // Kirim search query saat pindah halaman
                success: function(data) {
                    $('#table-body').html(data);
                    $('#loading-icon').addClass('hidden');
                }
            });
        });
    });
</script>
<script>
    // 1. LOGIKA TOMBOL DELETE (KONFIRMASI)
    document.getElementById('btn-reset').addEventListener('click', function(e) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Seluruh data survei akan dihapus permanen! Data tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33', // Merah
            cancelButtonColor: '#3085d6', // Biru
            confirmButtonText: 'Ya, Hapus Semua!',
            cancelButtonText: 'Batal',
            reverseButtons: true // Tombol batal di kiri, hapus di kanan
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik Ya, submit form secara manual
                document.getElementById('form-reset').submit();
            }
        })
    });

    // 2. LOGIKA NOTIFIKASI SUKSES (FLASH MESSAGE)
    // Cek apakah ada session 'success' dari Controller
    @if(session('success'))
    Swal.fire({
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        icon: 'success',
        confirmButtonColor: '#8C5E3C'
    });
    @endif

    // 3. LOGIKA NOTIFIKASI ERROR
    @if(session('error'))
    Swal.fire({
        title: 'Gagal!',
        text: "{{ session('error') }}",
        icon: 'error',
        confirmButtonColor: '#8C5E3C'
    });
    @endif
</script>
@endsection
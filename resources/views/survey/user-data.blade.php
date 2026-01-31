@extends('layouts.app')

@section('title', 'Isi Data Diri - Kopi Kita')

@section('content')
<div class="max-w-md w-full bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/50">

    <div class="text-center mb-8">
        <div class="inline-flex justify-center mb-4 text-coffee-primary">
            <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-coffee-dark">Data Pengunjung</h2>
        <p class="text-gray-500 text-sm mt-1">Mohon isi data diri sebelum mengisi survey</p>
    </div>

    <form action="{{ route('survey.process') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="block text-sm font-semibold text-coffee-dark mb-1.5">Nama Lengkap</label>
            <input type="text" id="name" name="name" required placeholder="Masukkan nama Anda..."
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-[#FDFBF9] focus:outline-none focus:ring-2 focus:ring-coffee-accent focus:border-transparent transition-all text-gray-800 placeholder-gray-400">
        </div>

        <div>
            <label for="email" class="block text-sm font-semibold text-coffee-dark mb-1.5">Email</label>
            <input type="email" id="email" name="email" required placeholder="contoh@email.com"
                class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-[#FDFBF9] focus:outline-none focus:ring-2 focus:ring-coffee-accent focus:border-transparent transition-all text-gray-800 placeholder-gray-400">
        </div>

        <button type="submit" class="w-full bg-coffee-accent hover:bg-coffee-accentHover text-white font-bold py-3.5 rounded-xl shadow-lg shadow-coffee-accent/20 transition-all transform hover:-translate-y-0.5 flex items-center justify-center gap-2 mt-2">
            <span>Lanjut ke Survey</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
            </svg>
        </button>
    </form>

    <div class="mt-8 text-center">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-1 text-sm text-coffee-primary hover:text-coffee-dark font-medium transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali ke Halaman Utama
        </a>
    </div>
</div>
@endsection
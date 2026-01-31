@extends('layouts.app')

@section('title', 'Selamat Datang - Kopi Kita')

@section('content')
<div class="text-center px-4 max-w-3xl mx-auto w-full">

    <div class="flex justify-center mb-6">
        <div class="text-coffee-primary p-4 rounded-full bg-white/40 backdrop-blur-sm shadow-sm border border-white/50">
            <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M18 8h1a4 4 0 0 1 0 8h-1"></path>
                <path d="M2 8h16v9a4 4 0 0 1-4 4H6a4 4 0 0 1-4-4V8z"></path>
                <line x1="6" y1="1" x2="6" y2="4"></line>
                <line x1="10" y1="1" x2="10" y2="4"></line>
                <line x1="14" y1="1" x2="14" y2="4"></line>
            </svg>
        </div>
    </div>

    <h1 class="text-6xl md:text-8xl font-bold text-coffee-primary mb-6 tracking-tight drop-shadow-sm">
        KOPI KITA
    </h1>

    <p class="text-coffee-dark text-lg md:text-xl mb-12 max-w-lg mx-auto leading-relaxed font-medium opacity-80">
        Tempat ngopi favorit dengan cita rasa khas dan pelayanan terbaik untuk Anda
    </p>

    <div class="flex flex-col sm:flex-row justify-center items-center gap-4">

        <a href="{{ route('survey.start') }}"
            class="group w-full sm:w-auto px-8 py-4 bg-coffee-accent text-white font-bold rounded-xl shadow-lg shadow-coffee-accent/20 hover:bg-coffee-accentHover transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3">
            <svg class="w-5 h-5 group-hover:animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span>Isi Survey</span>
        </a>

        @if (Route::has('login'))
        @auth
        <a href="{{ url('/dashboard') }}"
            class="w-full sm:w-auto px-8 py-4 bg-[#FDFBF9] text-coffee-dark border-2 border-[#E6E0DC] font-bold rounded-xl hover:bg-white hover:border-coffee-accent transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-coffee-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span>Dashboard</span>
        </a>
        @else
        <a href="{{ route('login') }}"
            class="w-full sm:w-auto px-8 py-4 bg-[#FDFBF9] text-coffee-dark border-2 border-[#E6E0DC] font-bold rounded-xl hover:bg-white hover:border-coffee-accent transition-all transform hover:-translate-y-1 flex items-center justify-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-coffee-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <span>Admin Dashboard</span>
        </a>
        @endauth
        @endif

    </div>

    <div class="mt-16 text-coffee-dark/40 text-sm font-medium">
        &copy; {{ date('Y') }} Kopi Kita. All rights reserved.
    </div>
</div>
@endsection
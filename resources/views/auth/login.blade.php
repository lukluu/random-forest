@extends('layouts.app')

@section('title', 'Login Admin - Kopi Kita')

@section('content')
<div class="flex w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden min-h-[550px]">

    <div class="hidden md:flex w-1/2 bg-coffee-accent items-center justify-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(circle_at_center,_var(--tw-gradient-stops))] from-white via-transparent to-transparent"></div>

        <div class="text-center relative z-10 p-10 text-white">
            <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-3 tracking-tight">Admin Area</h2>
            <p class="text-white/80 leading-relaxed font-light">
                Masuk untuk mengelola data survey,<br>melihat statistik, dan laporan.
            </p>
        </div>
    </div>

    <div class="w-full md:w-1/2 p-8 md:p-12 flex flex-col justify-center bg-white relative">

        <div class="mb-8">
            <h3 class="text-2xl font-bold text-coffee-dark">Selamat Datang</h3>
            <p class="text-gray-400 text-sm mt-1">Silakan masukkan akun admin Anda.</p>
        </div>

        @if (session('status'))
        <div class="mb-4 p-3 bg-green-50 text-green-600 text-sm rounded-lg border border-green-100">
            {{ session('status') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-coffee-dark mb-1.5">Nama Admin</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </span>
                    <input type="text" name="name" :value="old('name')" required autofocus placeholder="admin"
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-accent focus:border-transparent transition-all">
                </div>
                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-coffee-dark mb-1.5">Password</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </span>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full pl-11 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-coffee-accent focus:border-transparent transition-all">
                </div>
                @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex items-center justify-between mt-2">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded text-coffee-accent focus:ring-coffee-accent border-gray-300 w-4 h-4" name="remember">
                    <span class="ml-2 text-sm text-gray-500">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-coffee-dark hover:bg-coffee-accent text-white font-bold py-3.5 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5">
                MASUK DASHBOARD
            </button>
        </form>

        <div class="mt-8 text-center">
            <a href="{{ url('/') }}" class="text-sm text-gray-400 hover:text-coffee-accent transition-colors">
                Kembali ke Website
            </a>
        </div>
    </div>
</div>
@endsection
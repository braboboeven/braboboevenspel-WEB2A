@extends('layouts.game')

@section('content')
    <div class="game-lens game-lens-1"></div>
    <div class="game-lens game-lens-2"></div>
    <div class="game-lens game-lens-3"></div>
    <div class="game-lens game-lens-4"></div>

    <main class="relative z-10 flex min-h-screen flex-col items-center justify-center gap-3 text-center">
        <p class="text-3xl font-semibold">Het spel begint zo</p>
        <p class="text-lg text-zinc-300">Nog even geduld..</p>
        <a href="{{ route('spel') }}" class="mt-8 rounded-xl border border-white/20 bg-[#1a1a1d] px-6 py-2 text-sm uppercase tracking-[0.3em]">
            Verlaat spel
        </a>
    </main>
@endsection

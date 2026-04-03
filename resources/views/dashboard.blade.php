<x-layouts::app :title="__('Dashboard')">
    @php
        $groepCount = \App\Models\Groep::query()->count();
        $runningSessie = \App\Models\SpelSessie::query()->where('status', 'running')->latest()->first();
        $hintCount = \App\Models\HintVerzending::query()->count();
    @endphp

    <div class="game-shell rounded-2xl">
        <div class="game-container py-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="game-card-soft">
                    <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Status</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $runningSessie ? 'Spel actief' : 'Geen actieve sessie' }}</div>
                </div>
                <div class="game-card-soft">
                    <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Actieve groepen</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $groepCount }}</div>
                </div>
                <div class="game-card-soft">
                    <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Verstuurde hints</div>
                    <div class="mt-2 text-2xl font-semibold">{{ $hintCount }}</div>
                </div>
            </div>

            <div class="game-card mt-4">
                <div class="text-sm uppercase tracking-[0.3em] text-zinc-400">Snel naar</div>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <a href="{{ route('spel') }}" class="game-btn text-center">Spel</a>
                    <a href="{{ route('docent') }}" class="game-btn text-center">Docent</a>
                    <a href="{{ route('leaderboard') }}" class="game-btn text-center">Leaderboard</a>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

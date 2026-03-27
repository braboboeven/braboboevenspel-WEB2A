<?php

use App\Models\GroepScore;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Leaderboard'), Layout('layouts.game')] class extends Component {
    #[Computed]
    public function leaderboard(): Collection
    {
        return GroepScore::query()
            ->with('groep')
            ->orderByDesc('score')
            ->get();
    }

    #[Computed]
    public function bigBossLeaderboard(): Collection
    {
        return GroepScore::query()
            ->with('groep')
            ->orderByDesc('big_boss_score')
            ->get();
    }
}; ?>

<div class="min-h-screen bg-[#1a1a1d] text-white">
    <div class="mx-auto flex max-w-4xl flex-col gap-8 px-6 py-16">
        <h1 class="text-center text-3xl uppercase tracking-[0.35em]">leaderboard</h1>
        <div class="rounded-2xl bg-[#2e2e33] p-6">
            <div class="grid gap-3">
                @forelse ($this->leaderboard as $entry)
                    <div class="flex items-center justify-between rounded-xl bg-[#1a1a1d] px-4 py-3">
                        <div>
                            <div class="text-lg font-semibold">{{ $entry->groep?->naam ?? 'Onbekend' }}</div>
                            <div class="text-xs text-zinc-400">{{ $entry->groep?->klas ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">${{ $entry->score }}</div>
                            <div class="text-xs text-zinc-400">Big Boss: ${{ $entry->big_boss_score }}</div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-white/10 px-4 py-3 text-sm text-zinc-400">
                        Geen scores beschikbaar.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl bg-[#2e2e33] p-6">
            <h2 class="mb-4 text-sm uppercase tracking-[0.35em] text-zinc-300">big boss</h2>
            <div class="grid gap-3">
                @forelse ($this->bigBossLeaderboard as $entry)
                    <div class="flex items-center justify-between rounded-xl bg-[#1a1a1d] px-4 py-3">
                        <div>
                            <div class="text-lg font-semibold">{{ $entry->groep?->naam ?? 'Onbekend' }}</div>
                            <div class="text-xs text-zinc-400">{{ $entry->groep?->klas ?? '-' }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-semibold">${{ $entry->big_boss_score }}</div>
                            <div class="text-xs text-zinc-400">Basis: ${{ $entry->score }}</div>
                        </div>
                    </div>
                @empty
                    <div class="rounded-xl border border-white/10 px-4 py-3 text-sm text-zinc-400">
                        Geen Big Boss scores beschikbaar.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

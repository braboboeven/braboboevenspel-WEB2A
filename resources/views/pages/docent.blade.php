<?php

use App\Models\BigBossHint;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Docent dashboard'), Layout('layouts.game')] class extends Component {
    public string $hintType = 'normal';
    public ?int $selectedHintId = null;
    public array $selectedGroepen = [];
    public bool $broadcast = true;
    public ?string $statusMessage = null;

    public function mount(): void
    {
        if (! Auth::user()?->is_docent) {
            abort(403);
        }
    }

    public function startSpel(): void
    {
        SpelSessie::query()->create([
            'status' => 'running',
            'started_at' => now(),
            'created_by_user_id' => Auth::id(),
        ]);

        $this->statusMessage = 'Spel gestart.';
    }

    public function stopSpel(): void
    {
        $sessie = SpelSessie::query()->latest()->first();

        if ($sessie) {
            $sessie->update([
                'status' => 'stopped',
                'ended_at' => now(),
            ]);
        }

        $this->statusMessage = 'Spel gestopt.';
    }

    public function sendHint(): void
    {
        $validated = $this->validate([
            'hintType' => ['required', 'string'],
            'selectedHintId' => ['required', 'integer'],
        ]);

        $groepIds = $this->broadcast ? [] : $this->selectedGroepen;

        if (! $this->broadcast && empty($groepIds)) {
            $this->statusMessage = 'Selecteer minstens een groep of kies broadcast.';

            return;
        }

        $payload = [
            'hint_nummer' => $validated['hintType'] === 'normal' ? $validated['selectedHintId'] : null,
            'big_boss_hint_id' => $validated['hintType'] === 'bigboss' ? $validated['selectedHintId'] : null,
            'sent_by_user_id' => Auth::id(),
            'sent_at' => now(),
        ];

        if ($this->broadcast) {
            HintVerzending::create($payload);
        } else {
            foreach ($groepIds as $groepId) {
                HintVerzending::create($payload + ['groep_id' => $groepId]);
            }
        }

        $this->statusMessage = 'Hint verstuurd.';
    }

    #[Computed]
    public function groepen()
    {
        return Groep::query()->orderBy('naam')->get();
    }

    #[Computed]
    public function hints()
    {
        return Hint::query()->orderBy('hint_nummer')->get();
    }

    #[Computed]
    public function bigBossHints()
    {
        return BigBossHint::query()->orderBy('nummer')->get();
    }

    #[Computed]
    public function leaderboard()
    {
        return GroepScore::query()
            ->with('groep')
            ->orderByDesc('score')
            ->get();
    }

    #[Computed]
    public function sessie(): ?SpelSessie
    {
        return SpelSessie::query()->latest()->first();
    }

    #[Computed]
    public function elapsedFormatted(): string
    {
        $sessie = $this->sessie;
        if (! $sessie) {
            return '00:00';
        }

        return $sessie->elapsedFormatted();
    }
}; ?>

<div class="min-h-screen w-full bg-[#1a1a1d] text-white" wire:poll.1s="$refresh">
    <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 py-8 lg:px-8">
        <div class="grid gap-6 lg:grid-cols-[280px_1fr_220px]">
            <section class="rounded-2xl bg-[#2e2e33] p-4">
                <div class="text-lg uppercase tracking-[0.35em] text-zinc-200">Leader board</div>
                <div class="mt-4 space-y-2">
                    @forelse ($this->leaderboard as $entry)
                        <div class="flex items-center justify-between rounded-xl bg-[#1a1a1d] px-3 py-2 text-sm">
                            <div>
                                <div class="font-semibold">{{ $entry->groep?->naam ?? 'Onbekend' }}</div>
                                <div class="text-xs text-zinc-400">{{ $entry->groep?->klas ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-semibold">${{ $entry->score }}</div>
                                <div class="text-xs text-zinc-400">Big Boss: ${{ $entry->big_boss_score }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-white/10 px-3 py-2 text-sm text-zinc-400">
                            Geen scores beschikbaar.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl bg-[#242429] p-6">
                <div class="flex items-center justify-between">
                    <button type="button" class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/20 text-white/80">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                    </button>
                    <div class="rounded-xl border border-white/20 px-6 py-2 text-xl">Groep</div>
                    <button type="button" class="flex h-12 w-12 items-center justify-center rounded-xl border border-white/20 text-white/80">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 6l6 6-6 6" />
                        </svg>
                    </button>
                </div>

                <div class="mt-6 rounded-xl border border-white/10 p-6 text-center text-lg">
                    @php
                        $selectedHint = $hintType === 'normal'
                            ? $this->hints->firstWhere('hint_nummer', $selectedHintId)
                            : $this->bigBossHints->firstWhere('id', $selectedHintId);
                    @endphp

                    @if ($selectedHint)
                        <div class="text-sm uppercase tracking-[0.25em] text-zinc-400">Hint</div>
                        <div class="mt-3 text-2xl">
                            {{ $selectedHint->hint_beschrijving ?? $selectedHint->beschrijving }}
                        </div>
                    @else
                        <div class="text-2xl text-zinc-400">Vragen display</div>
                    @endif
                </div>

                <div class="mt-4 rounded-xl border border-white/20 px-4 py-6 text-center text-xl">
                    {{ $statusMessage ?? 'Goed antwoord' }}
                </div>

                <form wire:submit="sendHint" class="mt-6 grid gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Type hint</label>
                        <select wire:model="hintType" class="mt-2 w-full rounded-xl border border-white/10 bg-[#1a1a1d] px-3 py-2 text-sm text-white">
                            <option value="normal">Normale hint</option>
                            <option value="bigboss">Big Boss hint</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Hint</label>
                        <select wire:model="selectedHintId" class="mt-2 w-full rounded-xl border border-white/10 bg-[#1a1a1d] px-3 py-2 text-sm text-white">
                            <option value="">Selecteer hint</option>
                            @if ($hintType === 'normal')
                                @foreach ($this->hints as $hint)
                                    <option value="{{ $hint->hint_nummer }}">{{ $hint->hint_nummer }} - {{ $hint->hint_beschrijving }}</option>
                                @endforeach
                            @else
                                @foreach ($this->bigBossHints as $hint)
                                    <option value="{{ $hint->id }}">{{ $hint->nummer }} - {{ $hint->beschrijving }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-zinc-300">
                        <input type="checkbox" wire:model="broadcast" class="rounded border-white/20 bg-[#1a1a1d] text-emerald-400" />
                        Verstuur naar alle groepen
                    </label>

                    @if (! $broadcast)
                        <div class="max-h-40 overflow-y-auto rounded-xl border border-white/10 p-3 text-sm text-zinc-300">
                            @foreach ($this->groepen as $groep)
                                <label class="flex items-center gap-2 py-1">
                                    <input type="checkbox" value="{{ $groep->id }}" wire:model="selectedGroepen" class="rounded border-white/20 bg-[#1a1a1d] text-emerald-400" />
                                    {{ $groep->naam }} ({{ $groep->code }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="rounded-xl bg-white px-4 py-2 text-sm font-semibold text-[#1a1a1d]">
                        Verstuur hint
                    </button>
                </form>
            </section>

            <aside class="rounded-2xl bg-[#2e2e33] p-4">
                <div class="rounded-xl bg-black px-4 py-3 text-center text-2xl">
                    {{ $this->elapsedFormatted }}
                </div>
                <div class="mt-4 grid gap-2">
                    <button wire:click="startSpel" class="rounded-xl border border-white/10 bg-[#1a1a1d] px-4 py-2 text-sm">
                        Start spel
                    </button>
                    <button wire:click="stopSpel" class="rounded-xl border border-white/10 bg-[#1a1a1d] px-4 py-2 text-sm">
                        Stop spel
                    </button>
                </div>
                <div class="mt-3 text-xs uppercase tracking-[0.2em] text-zinc-400">
                    Status: {{ $this->sessie?->status ?? 'onbekend' }}
                </div>
            </aside>
        </div>
    </div>
</div>

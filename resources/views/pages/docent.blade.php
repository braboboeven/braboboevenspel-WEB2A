<?php

use App\Actions\Game\ManageVerdachteBank;
use App\Actions\Game\FinalizeGameSession;
use App\Models\BigBossHint;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\GroepVerdachteBank;
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
    public ?int $bankGroepId = null;
    public ?int $verdachteNummer = null;
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

    public function pauseSpel(): void
    {
        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            $this->statusMessage = 'Geen actieve sessie gevonden.';

            return;
        }

        $sessie->pause();
        $this->statusMessage = 'Spel gepauzeerd.';
    }

    public function resumeSpel(): void
    {
        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie) {
            $this->statusMessage = 'Geen actieve sessie gevonden.';

            return;
        }

        $sessie->resume();
        $this->statusMessage = 'Spel hervat.';
    }

    public function endSpel(FinalizeGameSession $finalizeGameSession): void
    {
        $sessie = SpelSessie::query()->latest()->first();
        $winner = $finalizeGameSession($sessie, true);

        $this->statusMessage = $winner['group_name']
            ? 'Spel beëindigd. Winnaar: '.$winner['group_name'].' ($'.$winner['total_score'].').'
            : 'Spel beëindigd en groepen opgeschoond.';
    }

    public function bankeerVerdachte(ManageVerdachteBank $manageVerdachteBank): void
    {
        $validated = $this->validate([
            'bankGroepId' => ['required', 'integer', 'exists:groeps,id'],
            'verdachteNummer' => ['required', 'integer', 'min:1'],
        ]);

        $result = $manageVerdachteBank->bankeer(
            (int) $validated['bankGroepId'],
            (int) $validated['verdachteNummer']
        );

        $this->statusMessage = $result['message'].' (+$'.$result['amount'].')';
    }

    public function confisqueerVerdachte(ManageVerdachteBank $manageVerdachteBank): void
    {
        $validated = $this->validate([
            'bankGroepId' => ['required', 'integer', 'exists:groeps,id'],
            'verdachteNummer' => ['required', 'integer', 'min:1'],
        ]);

        $result = $manageVerdachteBank->confisqueer(
            (int) $validated['bankGroepId'],
            (int) $validated['verdachteNummer']
        );

        $this->statusMessage = $result['message'].' (-$'.$result['amount'].')';
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
            ->with('groep.verdachteBanken')
            ->orderByDesc('score')
            ->get();
    }

    #[Computed]
    public function bankOverzicht()
    {
        return GroepVerdachteBank::query()
            ->with('groep')
            ->orderByDesc('banked_amount')
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

<div class="game-shell" wire:poll.1s="$refresh">
    <div class="game-container">
        <div class="grid gap-6 lg:grid-cols-[320px_1fr_280px]">
            <section class="game-card-soft">
                <div class="text-sm uppercase tracking-[0.35em] text-zinc-200">Leader board</div>
                <div class="mt-4 space-y-2">
                    @forelse ($this->leaderboard as $entry)
                        <div class="flex items-center justify-between rounded-xl border border-white/10 bg-zinc-900 px-3 py-2 text-sm">
                            <div>
                                <div class="font-semibold">{{ $entry->groep?->naam ?? 'Onbekend' }}</div>
                                <div class="text-xs text-zinc-400">{{ $entry->groep?->klas ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-base font-semibold">${{ $entry->score }}</div>
                                <div class="text-xs text-zinc-400">Big Boss: ${{ $entry->big_boss_score }}</div>
                                <div class="text-xs text-zinc-400">Bank: ${{ $entry->groep?->verdachteBanken?->sum('banked_amount') ?? 0 }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-white/10 px-3 py-2 text-sm text-zinc-400">
                            Geen scores beschikbaar.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6 text-xs uppercase tracking-[0.3em] text-zinc-400">Bank per verdachte</div>
                <div class="mt-3 max-h-64 space-y-2 overflow-y-auto">
                    @forelse ($this->bankOverzicht as $bank)
                        <div class="rounded-xl border border-white/10 bg-zinc-900 px-3 py-2 text-xs">
                            <div class="font-semibold">{{ $bank->groep?->naam ?? 'Onbekend' }} · Verdachte {{ $bank->verdachte_nummer }}</div>
                            <div class="text-zinc-400">Op bank: ${{ $bank->banked_amount }} · In beslag: ${{ $bank->confiscated_amount }}</div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-white/10 px-3 py-2 text-sm text-zinc-400">
                            Nog geen bankrecords.
                        </div>
                    @endforelse
                </div>
            </section>

            <section class="game-card">
                <div class="flex items-center justify-between">
                    <button type="button" class="game-btn flex h-12 w-12 items-center justify-center text-white/80">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                    </button>
                    <div class="game-panel text-xl">Groep</div>
                    <button type="button" class="game-btn flex h-12 w-12 items-center justify-center text-white/80">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 6l6 6-6 6" />
                        </svg>
                    </button>
                </div>

                <div class="mt-6 rounded-xl border border-white/10 bg-zinc-900 p-6 text-center text-lg">
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

                <div class="mt-4 rounded-xl border border-white/20 bg-zinc-900 px-4 py-6 text-center text-xl">
                    {{ $statusMessage ?? 'Goed antwoord' }}
                </div>

                <form wire:submit="sendHint" class="mt-6 grid gap-4">
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Type hint</label>
                        <select wire:model="hintType" class="game-input mt-2">
                            <option value="normal">Normale hint</option>
                            <option value="bigboss">Big Boss hint</option>
                        </select>
                    </div>

                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Hint</label>
                        <select wire:model="selectedHintId" class="game-input mt-2">
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
                        <input type="checkbox" wire:model="broadcast" class="rounded border-white/20 bg-zinc-900 text-emerald-400" />
                        Verstuur naar alle groepen
                    </label>

                    @if (! $broadcast)
                        <div class="max-h-40 overflow-y-auto rounded-xl border border-white/10 bg-zinc-900 p-3 text-sm text-zinc-300">
                            @foreach ($this->groepen as $groep)
                                <label class="flex items-center gap-2 py-1">
                                    <input type="checkbox" value="{{ $groep->id }}" wire:model="selectedGroepen" class="rounded border-white/20 bg-zinc-900 text-emerald-400" />
                                    {{ $groep->naam }} ({{ $groep->code }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <button type="submit" class="game-btn-primary">
                        Verstuur hint
                    </button>
                </form>

                <form class="mt-6 grid gap-4 border-t border-white/10 pt-6">
                    <div class="text-xs uppercase tracking-[0.2em] text-zinc-400">Bankbeheer verdachte</div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Groep</label>
                        <select wire:model="bankGroepId" class="game-input mt-2">
                            <option value="">Selecteer groep</option>
                            @foreach ($this->groepen as $groep)
                                <option value="{{ $groep->id }}">{{ $groep->naam }} ({{ $groep->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-xs uppercase tracking-[0.2em] text-zinc-400">Verdachte nummer</label>
                        <input type="number" min="1" wire:model="verdachteNummer" class="game-input mt-2" placeholder="Bijv. 3" />
                    </div>
                    <div class="grid gap-2 sm:grid-cols-2">
                        <button type="button" wire:click="bankeerVerdachte" class="game-btn-primary">Zet op bank</button>
                        <button type="button" wire:click="confisqueerVerdachte" class="game-btn">Confisqueer bij lek</button>
                    </div>
                </form>
            </section>

            <aside class="game-card-soft">
                <div class="game-panel bg-black text-center text-2xl">
                    {{ $this->elapsedFormatted }}
                </div>
                <div class="mt-4 grid gap-2">
                    <button wire:click="startSpel" class="game-btn">
                        Start spel
                    </button>
                    <button wire:click="pauseSpel" class="game-btn">
                        Pauzeer spel
                    </button>
                    <button wire:click="resumeSpel" class="game-btn">
                        Hervat spel
                    </button>
                    <button wire:click="endSpel" class="game-btn">
                        Eindig spel
                    </button>
                </div>
                <div class="mt-3 text-xs uppercase tracking-[0.2em] text-zinc-400">
                    Status: {{ $this->sessie?->status ?? 'onbekend' }}
                </div>
                @if ($this->sessie?->winner_group_name)
                    <div class="mt-3 rounded-xl border border-emerald-400/40 bg-zinc-900 px-3 py-2 text-sm">
                        Winnaar: {{ $this->sessie->winner_group_name }} (${{ $this->sessie->winner_total_score }})
                    </div>
                @endif
            </aside>
        </div>
    </div>
</div>

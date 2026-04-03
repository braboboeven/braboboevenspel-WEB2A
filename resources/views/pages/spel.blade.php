<?php

use App\Actions\Game\QueryEvaluator;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\HintVerzending;
use App\Models\Opdracht;
use App\Models\Poging;
use App\Models\SpelSessie;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Spel'), Layout('layouts.game')] class extends Component {
    public string $groepNaam = '';
    public string $groepKlas = '';
    public string $groepCode = '';
    public ?int $selectedOpdrachtId = null;
    public string $submittedQuery = '';
    public ?string $resultMessage = null;
    public ?string $correctQuery = null;
    public ?int $submittedCount = null;
    public ?int $correctCount = null;

    public function createGroep(): void
    {
        $validated = $this->validate([
            'groepNaam' => ['required', 'string', 'max:100'],
            'groepKlas' => ['nullable', 'string', 'max:20'],
        ]);

        $groep = Groep::create([
            'naam' => $validated['groepNaam'],
            'klas' => $validated['groepKlas'] ?: null,
            'code' => Str::upper(Str::random(8)),
        ]);

        $groep->gebruikers()->attach(Auth::id(), ['is_leider' => true]);

        $this->reset(['groepNaam', 'groepKlas']);
    }

    public function joinGroep(): void
    {
        $validated = $this->validate([
            'groepCode' => ['required', 'string', 'size:8'],
        ]);

        $groep = Groep::query()->where('code', $validated['groepCode'])->first();

        if (! $groep) {
            $this->resultMessage = 'Groepcode niet gevonden.';

            return;
        }

        $groep->gebruikers()->syncWithoutDetaching([Auth::id() => ['is_leider' => false]]);
        $this->reset(['groepCode']);
    }

    public function submitQuery(QueryEvaluator $evaluator): void
    {
        $sessie = SpelSessie::query()->latest()->first();

        if (! $sessie || $sessie->status !== 'running') {
            $this->resultMessage = 'Het spel is momenteel niet actief.';

            return;
        }

        $validated = $this->validate([
            'selectedOpdrachtId' => ['required', 'integer', 'exists:opdrachts,id'],
            'submittedQuery' => ['required', 'string'],
        ]);

        $groep = $this->groep;
        if (! $groep) {
            $this->resultMessage = 'Je moet eerst aan een groep gekoppeld zijn.';

            return;
        }

        $opdracht = Opdracht::query()->findOrFail($validated['selectedOpdrachtId']);

        $result = $evaluator->evaluate(
            $validated['submittedQuery'],
            $opdracht->correct_query,
            $opdracht->reward_correct,
            $opdracht->reward_bad_format
        );

        if (! $result['is_safe']) {
            $this->resultMessage = $result['error'];

            return;
        }

        if ($result['error']) {
            $this->resultMessage = 'Query fout: '.$result['error'];

            return;
        }

        $earned = (int) $result['earned'];
        if ($opdracht->is_big_boss && $result['is_correct'] && ! $result['is_good_format']) {
            $earned = max(0, $opdracht->reward_correct - 500);
        }

        if (! $opdracht->is_big_boss && $opdracht->verdachte_nummer) {
            $currentTotal = Poging::query()
                ->where('groep_id', $groep->id)
                ->whereHas('opdracht', fn ($query) => $query->where('verdachte_nummer', $opdracht->verdachte_nummer))
                ->sum('earned');

            $remaining = max(0, 5000 - $currentTotal);
            $earned = min($earned, $remaining);
        }

        Poging::create([
            'groep_id' => $groep->id,
            'opdracht_id' => $opdracht->id,
            'user_id' => Auth::id(),
            'submitted_query' => $validated['submittedQuery'],
            'is_correct' => $result['is_correct'],
            'is_good_format' => $result['is_good_format'],
            'earned' => $earned,
            'submitted_at' => now(),
        ]);

        $this->updateScore($groep, $opdracht, $earned);

        $this->submittedCount = $result['submitted_count'];
        $this->correctCount = $result['correct_count'];
        $this->correctQuery = $opdracht->correct_query;
        $this->resultMessage = $result['is_correct']
            ? 'Goed gedaan! Score toegevoegd.'
            : 'Niet correct, probeer het opnieuw.';

        if ($result['is_correct']) {
            $this->selectNextOpdracht();
        }

        $this->submittedQuery = '';
    }

    private function selectNextOpdracht(): void
    {
        if (! $this->selectedOpdrachtId) {
            return;
        }

        $opdrachtIds = $this->opdrachten->pluck('id')->values();
        $currentIndex = $opdrachtIds->search($this->selectedOpdrachtId);

        if ($currentIndex === false) {
            return;
        }

        $nextOpdrachtId = $opdrachtIds->get($currentIndex + 1);

        if ($nextOpdrachtId) {
            $this->selectedOpdrachtId = (int) $nextOpdrachtId;
        }
    }

    private function updateScore(Groep $groep, Opdracht $opdracht, int $earned): void
    {
        $score = GroepScore::query()->firstOrCreate(
            ['groep_id' => $groep->id],
            ['score' => 0, 'big_boss_score' => 0]
        );

        if ($opdracht->is_big_boss) {
            $score->big_boss_score = min(10000, $score->big_boss_score + $earned);
        } else {
            $score->score += $earned;
        }

        $score->last_submission_at = now();
        $score->save();
    }

    #[Computed]
    public function groep(): ?Groep
    {
        return Auth::user()?->groepen()->first();
    }

    #[Computed]
    public function opdrachten()
    {
        return Opdracht::query()->orderBy('code')->get();
    }

    #[Computed]
    public function selectedOpdracht(): ?Opdracht
    {
        if (! $this->selectedOpdrachtId) {
            return null;
        }

        return Opdracht::query()->find($this->selectedOpdrachtId);
    }

    #[Computed]
    public function ontvangenHints()
    {
        $groep = $this->groep;
        if (! $groep) {
            return collect();
        }

        return HintVerzending::query()
            ->with(['hint', 'bigBossHint'])
            ->where(function ($query) use ($groep) {
                $query->whereNull('groep_id')
                    ->orWhere('groep_id', $groep->id);
            })
            ->latest('sent_at')
            ->get();
    }

    #[Computed]
    public function spelSessie(): ?SpelSessie
    {
        return SpelSessie::query()->latest()->first();
    }

    #[Computed]
    public function elapsedFormatted(): string
    {
        $sessie = $this->spelSessie;
        if (! $sessie) {
            return '00:00';
        }

        return $sessie->elapsedFormatted();
    }

    #[Computed]
    public function gameIsActive(): bool
    {
        return $this->spelSessie?->status === 'running';
    }

    #[Computed]
    public function gameStatusLabel(): string
    {
        $status = $this->spelSessie?->status;

        return match ($status) {
            'running' => 'Spel actief',
            'paused' => 'Spel gepauzeerd',
            'stopped' => 'Spel beëindigd',
            default => 'Spel nog niet gestart',
        };
    }
}; ?>

<div class="game-shell" wire:poll.1s="$refresh">
    <div class="game-container max-w-6xl gap-8">
        @if ($this->spelSessie?->status === 'stopped' && $this->spelSessie?->winner_group_name)
            <div class="rounded-xl border border-emerald-400/40 bg-zinc-900 px-4 py-3 text-sm text-emerald-200">
                Winnaar: {{ $this->spelSessie->winner_group_name }} met ${{ $this->spelSessie->winner_total_score }}.
            </div>
        @endif

        @if (! $this->groep)
            <div class="flex flex-col items-center gap-8 text-center">
                <div class="rounded-2xl bg-zinc-900 px-8 py-4 text-3xl uppercase tracking-[0.25em]">
                    brabo-boevenspel
                </div>

                <div class="grid w-full max-w-4xl gap-6 lg:grid-cols-2">
                    <form wire:submit="joinGroep" class="game-card text-left">
                        <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Spel-code</div>
                        <input
                            wire:model="groepCode"
                            class="game-input-light mt-3 text-lg"
                            maxlength="8"
                            placeholder="00000000"
                            required
                        />
                        <button type="submit" class="game-btn-dark mt-4 w-full">
                            start
                        </button>
                    </form>

                    <form wire:submit="createGroep" class="game-card text-left">
                        <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Teamnaam</div>
                        <input
                            wire:model="groepNaam"
                            class="game-input-light mt-3 text-lg"
                            placeholder="Naam"
                            required
                        />
                        <div class="mt-4 text-xs uppercase tracking-[0.3em] text-zinc-400">Klas (optioneel)</div>
                        <input
                            wire:model="groepKlas"
                            class="game-input-light mt-3 text-lg"
                            placeholder="Klas"
                        />
                        <button type="submit" class="game-btn-dark mt-4 w-full">
                            maak groep
                        </button>
                    </form>
                </div>

                @if ($resultMessage)
                    <div class="rounded-xl border border-white/20 px-4 py-3 text-sm">
                        {{ $resultMessage }}
                    </div>
                @endif
            </div>
        @else
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div class="game-panel text-lg tracking-[0.4em]">
                    {{ $this->groep->code }}
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <div class="game-panel text-lg">
                        {{ $this->elapsedFormatted }}
                    </div>
                    <div class="game-panel text-lg">
                        {{ $this->selectedOpdracht?->code ?? '0' }}/{{ $this->opdrachten->count() }}
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="game-card">
                    <div class="text-lg text-zinc-200">
                        {{ $this->selectedOpdracht?->prompt ?? '-- De verdachte komt een gebouw uit. Het is een vrouw' }}
                    </div>

                    @unless ($this->gameIsActive)
                        <div class="mt-4 rounded-xl border border-yellow-400/60 bg-zinc-900 px-4 py-3 text-sm text-yellow-200">
                            {{ $this->gameStatusLabel }}. Antwoorden is tijdelijk uitgeschakeld.
                        </div>
                    @endunless

                    <form wire:submit="submitQuery" class="mt-6 grid gap-4">
                        <div>
                            <label class="text-xs uppercase tracking-[0.3em] text-zinc-400">Opdracht</label>
                            <select wire:model="selectedOpdrachtId" @disabled(! $this->gameIsActive) class="game-input mt-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="">Selecteer opdracht</option>
                                @foreach ($this->opdrachten as $opdracht)
                                    <option value="{{ $opdracht->id }}">
                                        {{ $opdracht->code }}{{ $opdracht->is_big_boss ? ' (Big Boss)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="rounded-xl bg-zinc-900 p-4">
                            <textarea
                                wire:model="submittedQuery"
                                rows="5"
                                @disabled(! $this->gameIsActive)
                                class="h-28 w-full resize-none bg-transparent text-center text-lg text-white outline-none disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="Antwoord"
                                required
                            ></textarea>
                        </div>

                        <button type="submit" @disabled(! $this->gameIsActive) class="game-btn-dark disabled:cursor-not-allowed disabled:opacity-50">
                            controleer
                        </button>
                    </form>

                    @php
                        $feedbackBorder = 'border-white/10';
                        if ($resultMessage) {
                            $feedbackBorder = str_contains($resultMessage, 'Goed')
                                ? 'border-emerald-400/70'
                                : 'border-red-400/70';
                        }
                    @endphp

                    @if ($resultMessage || $correctQuery)
                        <div class="mt-6 rounded-xl border {{ $feedbackBorder }} bg-zinc-900 p-4 text-sm">
                            @if ($resultMessage)
                                <div class="text-lg">{{ $resultMessage }}</div>
                            @endif
                            @if ($correctQuery)
                                <div class="mt-3 text-xs uppercase tracking-[0.2em] text-zinc-400">Juiste query</div>
                                <pre class="mt-2 whitespace-pre-wrap text-sm">{{ $correctQuery }}</pre>
                            @endif
                        </div>
                    @endif
                </div>

                <aside class="game-card-soft bg-zinc-900">
                    <div class="text-xs uppercase tracking-[0.3em] text-zinc-400">Hints</div>
                    <div class="mt-4 space-y-3">
                        @forelse ($this->ontvangenHints as $hint)
                            <div class="rounded-xl border border-white/10 bg-zinc-800 p-3 text-sm">
                                <div class="font-semibold">
                                    {{ $hint->hint?->hint_beschrijving ?? 'Big Boss hint' }}
                                </div>
                                @if ($hint->bigBossHint)
                                    <div class="text-xs text-zinc-400">{{ $hint->bigBossHint->beschrijving }}</div>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-xl border border-white/10 px-3 py-2 text-sm text-zinc-400">
                                Er zijn nog geen hints verstuurd.
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>
        @endif
    </div>
</div>

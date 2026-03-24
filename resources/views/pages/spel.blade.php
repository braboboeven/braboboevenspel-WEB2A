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
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Spel')] class extends Component {
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

        $this->submittedQuery = '';
    }

    private function updateScore(Groep $groep, Opdracht $opdracht, int $earned): void
    {
        $score = GroepScore::query()->firstOrCreate(
            ['groep_id' => $groep->id],
            ['score' => 0, 'big_boss_score' => 0]
        );

        if ($opdracht->is_big_boss) {
            $score->big_boss_score += $earned;
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
        if (! $sessie || ! $sessie->started_at) {
            return '00:00';
        }

        $endTime = match ($sessie->status) {
            'running' => now(),
            'paused' => $sessie->paused_at ?? now(),
            default => $sessie->ended_at ?? now(),
        };

        $elapsed = $endTime->diffInSeconds($sessie->started_at) - $sessie->total_paused_seconds;
        $elapsed = max(0, $elapsed);

        $minutes = str_pad((string) intdiv($elapsed, 60), 2, '0', STR_PAD_LEFT);
        $seconds = str_pad((string) ($elapsed % 60), 2, '0', STR_PAD_LEFT);

        return $minutes.':'.$seconds;
    }
}; ?>

<x-layouts::app :title="__('Spel')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl" wire:poll.1s>
        <div class="flex flex-col gap-2">
            <flux:heading size="lg">Spelstatus</flux:heading>
            <flux:text>Timer: {{ $this->elapsedFormatted }}</flux:text>
            <flux:text>Status: {{ $this->spelSessie?->status ?? 'onbekend' }}</flux:text>
        </div>

        @if (! $this->groep)
            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="md">Nieuwe groep</flux:heading>
                    <form wire:submit="createGroep" class="mt-4 space-y-3">
                        <flux:input wire:model="groepNaam" label="Groepsnaam" required />
                        <flux:input wire:model="groepKlas" label="Klas (optioneel)" />
                        <flux:button variant="primary" type="submit">Maak groep</flux:button>
                    </form>
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="md">Sluit aan bij groep</flux:heading>
                    <form wire:submit="joinGroep" class="mt-4 space-y-3">
                        <flux:input wire:model="groepCode" label="Groepcode" required />
                        <flux:button variant="primary" type="submit">Aansluiten</flux:button>
                    </form>
                </div>
            </div>
        @else
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="md">Groep</flux:heading>
                <flux:text>Naam: {{ $this->groep->naam }} ({{ $this->groep->code }})</flux:text>
                <flux:text>Klas: {{ $this->groep->klas ?? '-' }}</flux:text>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="md">Query insturen</flux:heading>
                    <form wire:submit="submitQuery" class="mt-4 space-y-3">
                        <label class="text-sm font-medium">Opdracht</label>
                        <select wire:model="selectedOpdrachtId" class="w-full rounded-md border border-neutral-300 bg-white p-2 text-sm dark:border-neutral-600 dark:bg-neutral-900">
                            <option value="">Selecteer opdracht</option>
                            @foreach ($this->opdrachten as $opdracht)
                                <option value="{{ $opdracht->id }}">
                                    {{ $opdracht->code }}{{ $opdracht->is_big_boss ? ' (Big Boss)' : '' }}
                                </option>
                            @endforeach
                        </select>

                        @if ($this->selectedOpdracht)
                            <div class="rounded-md border border-neutral-200 p-2 text-sm dark:border-neutral-700">
                                {{ $this->selectedOpdracht->prompt }}
                            </div>
                        @endif

                        <flux:textarea wire:model="submittedQuery" label="Query" rows="6" required />
                        <flux:button variant="primary" type="submit">Verstuur query</flux:button>
                    </form>

                    @if ($resultMessage)
                        <flux:text class="mt-4">{{ $resultMessage }}</flux:text>
                    @endif

                    @if ($correctQuery)
                        <div class="mt-4 rounded-md bg-neutral-100 p-3 text-sm text-neutral-800 dark:bg-neutral-900 dark:text-neutral-200">
                            <div class="font-semibold">Juiste query</div>
                            <pre class="mt-2 whitespace-pre-wrap">{{ $correctQuery }}</pre>
                        </div>
                    @endif
                </div>

                <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                    <flux:heading size="md">Hints</flux:heading>
                    <div class="mt-4 space-y-3">
                        @forelse ($this->ontvangenHints as $hint)
                            <div class="rounded-md border border-neutral-200 p-3 text-sm dark:border-neutral-700">
                                <div class="font-semibold">
                                    {{ $hint->hint?->hint_beschrijving ?? 'Big Boss hint' }}
                                </div>
                                @if ($hint->bigBossHint)
                                    <div class="text-xs text-neutral-500">{{ $hint->bigBossHint->beschrijving }}</div>
                                @endif
                            </div>
                        @empty
                            <flux:text>Er zijn nog geen hints verstuurd.</flux:text>
                        @endforelse
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-layouts::app>

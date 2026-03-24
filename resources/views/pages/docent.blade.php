<?php

use App\Models\BigBossHint;
use App\Models\Groep;
use App\Models\GroepScore;
use App\Models\Hint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Docent dashboard')] class extends Component {
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
}; ?>

<x-layouts::app :title="__('Docent dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
            <flux:heading size="md">Spel beheren</flux:heading>
            <div class="mt-3 flex flex-wrap gap-2">
                <flux:button wire:click="startSpel" variant="primary">Start spel</flux:button>
                <flux:button wire:click="stopSpel" variant="danger">Stop spel</flux:button>
            </div>
            <flux:text class="mt-2">Status: {{ $this->sessie?->status ?? 'onbekend' }}</flux:text>
            @if ($statusMessage)
                <flux:text class="mt-2">{{ $statusMessage }}</flux:text>
            @endif
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="md">Hints versturen</flux:heading>
                <form wire:submit="sendHint" class="mt-4 space-y-3">
                    <label class="text-sm font-medium">Type hint</label>
                    <select wire:model="hintType" class="w-full rounded-md border border-neutral-300 bg-white p-2 text-sm dark:border-neutral-600 dark:bg-neutral-900">
                        <option value="normal">Normale hint</option>
                        <option value="bigboss">Big Boss hint</option>
                    </select>

                    <label class="text-sm font-medium">Hint</label>
                    <select wire:model="selectedHintId" class="w-full rounded-md border border-neutral-300 bg-white p-2 text-sm dark:border-neutral-600 dark:bg-neutral-900">
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

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" wire:model="broadcast" />
                        Verstuur naar alle groepen
                    </label>

                    @if (! $broadcast)
                        <div class="max-h-48 overflow-y-auto rounded-md border border-neutral-200 p-2 text-sm dark:border-neutral-700">
                            @foreach ($this->groepen as $groep)
                                <label class="flex items-center gap-2 py-1">
                                    <input type="checkbox" value="{{ $groep->id }}" wire:model="selectedGroepen" />
                                    {{ $groep->naam }} ({{ $groep->code }})
                                </label>
                            @endforeach
                        </div>
                    @endif

                    <flux:button variant="primary" type="submit">Verstuur hint</flux:button>
                </form>
            </div>

            <div class="rounded-xl border border-neutral-200 p-4 dark:border-neutral-700">
                <flux:heading size="md">Leaderboard</flux:heading>
                <div class="mt-4 space-y-2">
                    @forelse ($this->leaderboard as $entry)
                        <div class="flex items-center justify-between rounded-md border border-neutral-200 p-2 text-sm dark:border-neutral-700">
                            <div>
                                <div class="font-semibold">{{ $entry->groep?->naam ?? 'Onbekend' }}</div>
                                <div class="text-xs text-neutral-500">{{ $entry->groep?->klas ?? '-' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-semibold">${{ $entry->score }}</div>
                                <div class="text-xs text-neutral-500">Big Boss: ${{ $entry->big_boss_score }}</div>
                            </div>
                        </div>
                    @empty
                        <flux:text>Geen scores beschikbaar.</flux:text>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>

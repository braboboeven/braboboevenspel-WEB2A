<?php

namespace App\Console\Commands;

use App\Models\BigBossHint;
use App\Models\HintVerzending;
use App\Models\SpelSessie;
use Illuminate\Console\Command;

class SendBigBossHint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spel:send-big-boss-hint';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the next big boss hint every 24 hours during an active game';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $sessie = SpelSessie::query()
            ->whereIn('status', ['running', 'paused'])
            ->latest()
            ->first();

        if (! $sessie || ! $sessie->started_at) {
            return self::SUCCESS;
        }

        $elapsedSeconds = $sessie->elapsedSeconds();
        $dueHints = intdiv($elapsedSeconds, 86400);

        if ($dueHints < 1) {
            return self::SUCCESS;
        }

        $sentHints = HintVerzending::query()
            ->whereNotNull('big_boss_hint_id')
            ->where('sent_at', '>=', $sessie->started_at)
            ->distinct()
            ->count('big_boss_hint_id');

        $remainingHints = $dueHints - $sentHints;
        if ($remainingHints < 1) {
            return self::SUCCESS;
        }

        $bigBossHints = BigBossHint::query()
            ->orderBy('nummer')
            ->skip($sentHints)
            ->take($remainingHints)
            ->get();

        foreach ($bigBossHints as $hint) {
            HintVerzending::create([
                'big_boss_hint_id' => $hint->id,
                'sent_by_user_id' => null,
                'sent_at' => now(),
            ]);
        }

        return self::SUCCESS;
    }
}

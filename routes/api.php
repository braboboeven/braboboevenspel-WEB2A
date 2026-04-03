<?php

use App\Http\Controllers\Api\V1\DocentController;
use App\Http\Controllers\Api\V1\GroepController;
use App\Http\Controllers\Api\V1\HintController;
use App\Http\Controllers\Api\V1\LeaderboardController;
use App\Http\Controllers\Api\V1\OpdrachtController;
use App\Http\Controllers\Api\V1\SpelController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('v1')->group(function () {
    Route::get('groepen/me', [GroepController::class, 'me']);
    Route::post('groepen', [GroepController::class, 'store']);
    Route::post('groepen/join', [GroepController::class, 'join']);

    Route::get('opdrachten', [OpdrachtController::class, 'index']);
    Route::post('opdrachten/submit', [OpdrachtController::class, 'submit']);

    Route::get('hints', [HintController::class, 'index']);
    Route::get('leaderboard', [LeaderboardController::class, 'index']);
    Route::get('leaderboard/big-boss', [LeaderboardController::class, 'bigBoss']);
    Route::get('spel/sessie', [SpelController::class, 'status']);

    Route::post('docent/spel/start', [DocentController::class, 'startSpel']);
    Route::post('docent/spel/pause', [DocentController::class, 'pauseSpel']);
    Route::post('docent/spel/resume', [DocentController::class, 'resumeSpel']);
    Route::post('docent/spel/end', [DocentController::class, 'endSpel']);
    Route::post('docent/spel/stop', [DocentController::class, 'stopSpel']);
    Route::post('docent/hints', [DocentController::class, 'sendHint']);
    Route::get('docent/groepen', [DocentController::class, 'groepen']);
    Route::get('docent/hints/options', [DocentController::class, 'hintOptions']);
});

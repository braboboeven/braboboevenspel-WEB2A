<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="game-shell antialiased">
        <div class="game-container min-h-screen justify-between py-10">
            <header class="flex items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="game-panel text-sm uppercase tracking-[0.3em]">
                    Brabo-Boevenspel
                </a>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="game-btn" wire:navigate>
                            Dashboard
                        </a>
                        <a href="{{ route('spel') }}" class="game-btn-primary" wire:navigate>
                            Naar spel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="game-btn">
                            Inloggen
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="game-btn-primary">
                                Registreren
                            </a>
                        @endif
                    @endauth
                </div>
            </header>

            <main class="grid gap-8 py-10 lg:grid-cols-[1fr_340px] lg:items-center">
                <section class="game-card">
                    <p class="text-xs uppercase tracking-[0.35em] text-zinc-400">SQL Detective Game</p>
                    <h1 class="mt-4 text-4xl font-semibold leading-tight">
                        Vind de Big Boss met slimme SQL-vragen.
                    </h1>
                    <p class="mt-4 max-w-2xl text-zinc-300">
                        Werk in team, los opdrachten op, verdien punten en volg hints om de verdachte te ontmaskeren.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('spel') }}" class="game-btn-primary" wire:navigate>
                                Start spel
                            </a>
                            <a href="{{ route('leaderboard') }}" class="game-btn" wire:navigate>
                                Bekijk leaderboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="game-btn-primary">
                                Inloggen om te spelen
                            </a>
                        @endauth
                    </div>
                </section>

                <aside class="game-card-soft">
                    <h2 class="text-sm uppercase tracking-[0.3em] text-zinc-400">Snel naar</h2>
                    <div class="mt-4 grid gap-3">
                        <a href="{{ route('regels') }}" class="game-btn" wire:navigate>
                            Spelregels
                        </a>
                        <a href="{{ route('leaderboard') }}" class="game-btn" wire:navigate>
                            Leaderboard
                        </a>
                        @auth
                            @if (auth()->user()?->is_docent)
                                <a href="{{ route('docent') }}" class="game-btn" wire:navigate>
                                    Docentpaneel
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}" class="game-btn" wire:navigate>
                                Dashboard
                            </a>
                        @endif
                    </div>
                </aside>
            </main>

            <footer class="pt-6 text-xs uppercase tracking-[0.2em] text-zinc-500">
                Brabo-Boevenspel
            </footer>
        </div>

        @livewireScripts
        @fluxScripts
    </body>
</html>

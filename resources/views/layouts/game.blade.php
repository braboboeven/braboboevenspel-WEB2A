<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#1a1a1d] text-white antialiased font-game">
        {{ $slot ?? '' }}
        @yield('content')
        @livewireScripts
        @fluxScripts
    </body>
</html>

@extends('layouts.game')

@section('content')
    <div class="min-h-screen bg-[#1a1a1d] text-white">
        <div class="mx-auto flex max-w-4xl flex-col gap-10 px-6 py-16">
            <h1 class="text-center text-3xl uppercase tracking-[0.35em]">spelregels</h1>
            <div class="rounded-2xl bg-[#2e2e33] p-8 text-sm leading-7 text-zinc-200">
                <ol class="space-y-6">
                    <li><span class="text-zinc-400">1.</span> het spel is in groepjes behalve als je een kleine klas hebt kan het individueel dus iedereen apart.</li>
                    <li><span class="text-zinc-400">2.</span> ieder groepje speelt op 1 laptop.</li>
                    <li><span class="text-zinc-400">3.</span> de docent heeft een dashboard en houdt de bankrekening bij.</li>
                    <li><span class="text-zinc-400">4.</span> per verdachte moet een groepje 5 SQL statements invoeren.</li>
                    <li><span class="text-zinc-400">5.</span> per goede query en goede layout: 1000 dollar.</li>
                    <li><span class="text-zinc-400">6.</span> werkend maar geen goede format: 500 dollar.</li>
                    <li><span class="text-zinc-400">7.</span> na elke poging krijg je de juiste query zodat je verder kunt.</li>
                    <li><span class="text-zinc-400">8.</span> per verdachte is maximaal 5000 dollar te verdienen.</li>
                    <li><span class="text-zinc-400">9.</span> naam van de verdachte en je queries blijven geheim. bij lekken wordt het geld voor die verdachte afgenomen.</li>
                    <li><span class="text-zinc-400">10.</span> big boss: elke les komen er hints. met de big boss is 10.000 dollar te winnen (-500 per onjuist format).</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

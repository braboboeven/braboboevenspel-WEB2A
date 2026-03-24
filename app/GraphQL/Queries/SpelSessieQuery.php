<?php

namespace App\GraphQL\Queries;

use App\Models\SpelSessie;

class SpelSessieQuery
{
    public function current(): ?SpelSessie
    {
        return SpelSessie::query()->latest()->first();
    }
}

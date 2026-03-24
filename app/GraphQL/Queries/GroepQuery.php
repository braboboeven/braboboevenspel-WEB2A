<?php

namespace App\GraphQL\Queries;

use App\Models\Groep;
use Illuminate\Support\Facades\Auth;

class GroepQuery
{
    public function me(): ?Groep
    {
        return Auth::user()?->groepen()->first();
    }
}

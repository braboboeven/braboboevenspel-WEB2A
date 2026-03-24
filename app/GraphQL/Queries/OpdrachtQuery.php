<?php

namespace App\GraphQL\Queries;

use App\Models\Opdracht;

class OpdrachtQuery
{
    /**
     * @return array<int, Opdracht>
     */
    public function all(?object $root, array $args): array
    {
        $query = Opdracht::query()->orderBy('code');

        if (array_key_exists('big_boss', $args)) {
            $query->where('is_big_boss', (bool) $args['big_boss']);
        }

        return $query->get()->all();
    }
}

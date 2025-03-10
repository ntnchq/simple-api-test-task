<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTagsByIds;

final class GetTagsByIdsQuery
{
    /**
     * @param array<int> $ids
     */
    public function __construct(
        public readonly array $ids,
    ) {
    }
}

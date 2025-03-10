<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTagList;

final class GetTagListQuery
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 10,
    ) {
    }
}

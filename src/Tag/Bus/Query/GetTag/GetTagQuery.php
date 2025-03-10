<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTag;

final class GetTagQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

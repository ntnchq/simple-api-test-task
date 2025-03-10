<?php

declare(strict_types=1);

namespace App\Article\Bus\Query\GetArticle;

final class GetArticleQuery
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

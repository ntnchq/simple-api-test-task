<?php

declare(strict_types=1);

namespace App\Article\Bus\Query\GetArticleList;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @immutable
 */
final class GetArticleListQuery
{
    /**
     * @param array<int, int> $tagIds
     */
    public function __construct(
        /** @var array<int, int> */
        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\PositiveOrZero(),
        ])]
        public readonly array $tagIds = [],
        #[Assert\Positive]
        public readonly int $page = 1,
        #[Assert\Positive]
        public readonly int $limit = 10,
    ) {
    }
}

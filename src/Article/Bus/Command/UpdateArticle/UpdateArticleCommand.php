<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\UpdateArticle;

final class UpdateArticleCommand
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly array $tagIds = [],
    ) {
    }
}

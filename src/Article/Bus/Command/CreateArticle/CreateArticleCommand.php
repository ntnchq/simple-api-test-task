<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\CreateArticle;

final class CreateArticleCommand
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        public readonly string $title,
        public readonly array $tagIds = [],
    ) {
    }
}

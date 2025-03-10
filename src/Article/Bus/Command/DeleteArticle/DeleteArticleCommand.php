<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\DeleteArticle;

final class DeleteArticleCommand
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

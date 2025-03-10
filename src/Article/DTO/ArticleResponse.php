<?php

declare(strict_types=1);

namespace App\Article\DTO;

use App\Article\Entity\Article;
use App\Article\Entity\ArticleTag;
use App\Tag\DTO\TagResponse;

final class ArticleResponse
{
    /**
     * @param array<TagResponse> $tags
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly array $tags,
    ) {
    }

    public static function fromEntity(Article $article): self
    {
        $tags = array_map(
            fn (ArticleTag $articleTag) => TagResponse::fromEntity(
                tag: $articleTag->getTag(),
                attachedAt: $articleTag->getCreatedAt()
            ),
            $article->getArticleTags()->toArray()
        );

        return new self(
            id: $article->getId(),
            title: $article->getTitle(),
            tags: $tags,
        );
    }
}

<?php

declare(strict_types=1);

namespace App\Api\DTO\Article;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateArticleRequest
{
    /**
     * @param array<int> $tagIds
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public readonly string $title,
        #[Assert\All([
            new Assert\Type('integer'),
            new Assert\Positive(),
        ])]
        public readonly array $tagIds = [],
    ) {
    }
}

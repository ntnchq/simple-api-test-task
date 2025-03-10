<?php

declare(strict_types=1);

namespace App\Tag\DTO;

use App\Tag\Entity\Tag;

final class TagResponse
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?\DateTimeImmutable $attachedAt = null,
    ) {
    }

    public static function fromEntity(Tag $tag, ?\DateTimeImmutable $attachedAt = null): self
    {
        return new self(
            id: $tag->getId(),
            name: $tag->getName(),
            attachedAt: $attachedAt,
        );
    }
}

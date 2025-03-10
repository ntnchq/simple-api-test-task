<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\UpdateTag;

final class UpdateTagCommand
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
    ) {
    }
}

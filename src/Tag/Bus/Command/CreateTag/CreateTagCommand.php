<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\CreateTag;

final class CreateTagCommand
{
    public function __construct(
        public readonly string $name,
    ) {
    }
}

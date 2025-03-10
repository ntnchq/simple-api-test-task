<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\DeleteTag;

final class DeleteTagCommand
{
    public function __construct(
        public readonly int $id,
    ) {
    }
}

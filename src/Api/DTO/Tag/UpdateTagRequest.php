<?php

declare(strict_types=1);

namespace App\Api\DTO\Tag;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateTagRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        private readonly string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}

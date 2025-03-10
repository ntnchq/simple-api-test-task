<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use Doctrine\ORM\Mapping as ORM;

trait IntegerIdTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): int
    {
        if ($this->id === null) {
            throw new \LogicException('Entity ID is not set.');
        }

        return $this->id;
    }
}

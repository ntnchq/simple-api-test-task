<?php

declare(strict_types=1);

namespace App\Tag\Entity;

use App\Shared\Entity\IntegerIdTrait;
use App\Shared\Entity\TimestampableTrait;
use App\Tag\Repository\TagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TagRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(columns: ['name'])]
#[ORM\Index(columns: ['name'])]
class Tag
{
    use IntegerIdTrait;
    use TimestampableTrait;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,
    ) {
        $this->fillTimestampable();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}

<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\CreateTag;

use App\Tag\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTagHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateTagCommand $command): Tag
    {
        $tag = new Tag($command->name);

        $this->entityManager->persist($tag);
        $this->entityManager->flush();

        return $tag;
    }
}

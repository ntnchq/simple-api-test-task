<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\UpdateTag;

use App\Tag\Entity\Tag;
use App\Tag\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateTagHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function __invoke(UpdateTagCommand $command): Tag
    {
        $tag = $this->tagRepository->find($command->id);
        if (!$tag) {
            throw new NotFoundHttpException(\sprintf('Tag with ID %d not found', $command->id));
        }

        $tag->setName($command->name);
        $this->entityManager->flush();

        return $tag;
    }
}

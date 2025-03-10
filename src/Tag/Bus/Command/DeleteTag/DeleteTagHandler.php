<?php

declare(strict_types=1);

namespace App\Tag\Bus\Command\DeleteTag;

use App\Tag\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteTagHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function __invoke(DeleteTagCommand $command): void
    {
        $tag = $this->tagRepository->find($command->id);
        if (!$tag) {
            throw new NotFoundHttpException(\sprintf('Tag with ID %d not found', $command->id));
        }

        $this->entityManager->remove($tag);
        $this->entityManager->flush();
    }
}

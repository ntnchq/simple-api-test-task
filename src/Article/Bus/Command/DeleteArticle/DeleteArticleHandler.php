<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\DeleteArticle;

use App\Article\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteArticleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(DeleteArticleCommand $command): void
    {
        $article = $this->articleRepository->find($command->id);
        if (!$article) {
            throw new NotFoundHttpException(\sprintf('Article with ID %d not found', $command->id));
        }

        $this->entityManager->remove($article);
        $this->entityManager->flush();
    }
}

<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\UpdateArticle;

use App\Article\Entity\Article;
use App\Article\Repository\ArticleRepository;
use App\Shared\Bus\QueryBus;
use App\Tag\Bus\Query\GetTagsByIds\GetTagsByIdsQuery;
use App\Tag\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class UpdateArticleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly ArticleRepository $articleRepository,
        private readonly QueryBus $queryBus,
    ) {
    }

    public function __invoke(UpdateArticleCommand $command): Article
    {
        $article = $this->articleRepository->find(id: $command->id);
        if (!$article) {
            throw new NotFoundHttpException(message: \sprintf('Article with ID %d not found', $command->id));
        }

        $article->setTitle(title: $command->title);

        foreach ($article->getTags() as $tag) {
            $article->removeTag(tag: $tag);
        }
        $this->entityManager->flush();

        if ($command->tagIds !== []) {
            $query = new GetTagsByIdsQuery(ids: $command->tagIds);
            /** @var array<Tag> $tags */
            $tags = $this->queryBus->dispatch($query);
            foreach ($tags as $tag) {
                $article->addTag(tag: $tag);
            }
        }

        $this->entityManager->flush();

        return $article;
    }
}

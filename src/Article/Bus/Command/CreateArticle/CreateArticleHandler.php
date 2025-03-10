<?php

declare(strict_types=1);

namespace App\Article\Bus\Command\CreateArticle;

use App\Article\Entity\Article;
use App\Shared\Bus\QueryBus;
use App\Tag\Bus\Query\GetTagsByIds\GetTagsByIdsQuery;
use App\Tag\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateArticleHandler
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly QueryBus $queryBus,
    ) {
    }

    public function __invoke(CreateArticleCommand $command): Article
    {
        $article = new Article(title: $command->title);

        if ($command->tagIds !== []) {
            $query = new GetTagsByIdsQuery(ids: $command->tagIds);
            /** @var array<Tag> $tags */
            $tags = $this->queryBus->dispatch($query);
            foreach ($tags as $tag) {
                $article->addTag(tag: $tag);
            }
        }

        $this->entityManager->persist($article);
        $this->entityManager->flush();

        return $article;
    }
}

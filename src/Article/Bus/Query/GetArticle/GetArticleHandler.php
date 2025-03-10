<?php

declare(strict_types=1);

namespace App\Article\Bus\Query\GetArticle;

use App\Article\DTO\ArticleResponse;
use App\Article\Repository\ArticleRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetArticleHandler
{
    public function __construct(
        private readonly ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(GetArticleQuery $query): ArticleResponse
    {
        $article = $this->articleRepository->find($query->id);
        if (!$article) {
            throw new NotFoundHttpException(\sprintf('Article with ID %d not found', $query->id));
        }

        return ArticleResponse::fromEntity($article);
    }
}

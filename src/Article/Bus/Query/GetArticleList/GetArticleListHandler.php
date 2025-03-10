<?php

declare(strict_types=1);

namespace App\Article\Bus\Query\GetArticleList;

use App\Article\DTO\ArticleResponse;
use App\Article\Repository\ArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleListHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    /**
     * @return array<int, ArticleResponse>
     */
    public function __invoke(GetArticleListQuery $query): array
    {
        $offset = ($query->page - 1) * $query->limit;
        $articles = $this->articleRepository->findByTagsPaginated(
            tagIds: $query->tagIds,
            offset: $offset,
            limit: $query->limit
        );

        /** @var array<int, ArticleResponse> */
        return array_map(
            static fn ($article) => ArticleResponse::fromEntity(article: $article),
            $articles
        );
    }
}

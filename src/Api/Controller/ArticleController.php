<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\DTO\Article\CreateArticleRequest;
use App\Api\DTO\Article\UpdateArticleRequest;
use App\Article\Bus\Command\CreateArticle\CreateArticleCommand;
use App\Article\Bus\Command\DeleteArticle\DeleteArticleCommand;
use App\Article\Bus\Command\UpdateArticle\UpdateArticleCommand;
use App\Article\Bus\Query\GetArticle\GetArticleQuery;
use App\Article\Bus\Query\GetArticleList\GetArticleListQuery;
use App\Article\DTO\ArticleResponse;
use App\Article\Entity\Article;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/articles')]
#[OA\Tag(name: 'Articles')]
final class ArticleController extends AbstractApiController
{
    /**
     * @param array<int> $tagIds
     */
    #[Route('', name: 'article_list', methods: ['GET'])]
    #[OA\Parameter(
        name: 'tagIds[]',
        description: 'Filter articles by tag IDs',
        in: 'query',
        schema: new OA\Schema(type: 'array', items: new OA\Items(type: 'integer')),
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 1),
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Number of items per page',
        in: 'query',
        schema: new OA\Schema(type: 'integer', default: 10),
    )]
    #[OA\Response(
        response: 200,
        description: 'Returns a list of articles',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: ArticleResponse::class))
        )
    )]
    public function list(
        #[MapQueryParameter]
        int $page = 1,
        #[MapQueryParameter]
        int $limit = 10,
        /** @var array<int|string> */
        #[MapQueryParameter]
        array $tagIds = [],
    ): JsonResponse {
        $query = new GetArticleListQuery(
            tagIds: array_map(static fn ($id) => (int) $id, $tagIds),
            page: $page,
            limit: $limit
        );
        $articles = $this->queryBus->dispatch($query);

        return $this->json($articles);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns an article',
        content: new OA\JsonContent(ref: new Model(type: ArticleResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Article not found'
    )]
    public function show(int $id): JsonResponse
    {
        $query = new GetArticleQuery(id: $id);
        $article = $this->queryBus->dispatch($query);

        return $this->json($article);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Article data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateArticleRequest::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Article created',
        content: new OA\JsonContent(ref: new Model(type: ArticleResponse::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    public function create(
        #[MapRequestPayload]
        CreateArticleRequest $createRequest
    ): JsonResponse {
        $command = new CreateArticleCommand(
            title: $createRequest->title,
            tagIds: $createRequest->tagIds
        );
        /** @var Article $article */
        $article = $this->commandBus->dispatch($command);

        return $this->json(
            ArticleResponse::fromEntity($article),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'Article data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: UpdateArticleRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Article updated',
        content: new OA\JsonContent(ref: new Model(type: ArticleResponse::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    #[OA\Response(
        response: 404,
        description: 'Article not found'
    )]
    public function update(
        int $id,
        #[MapRequestPayload]
        UpdateArticleRequest $updateRequest
    ): JsonResponse {
        $command = new UpdateArticleCommand(
            id: $id,
            title: $updateRequest->title,
            tagIds: $updateRequest->tagIds
        );
        /** @var Article $article */
        $article = $this->commandBus->dispatch($command);

        return $this->json(ArticleResponse::fromEntity($article));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Article deleted'
    )]
    #[OA\Response(
        response: 404,
        description: 'Article not found'
    )]
    public function delete(int $id): JsonResponse
    {
        $command = new DeleteArticleCommand(id: $id);
        $this->commandBus->dispatch($command);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Api\DTO\Tag\CreateTagRequest;
use App\Api\DTO\Tag\UpdateTagRequest;
use App\Tag\Bus\Command\CreateTag\CreateTagCommand;
use App\Tag\Bus\Command\DeleteTag\DeleteTagCommand;
use App\Tag\Bus\Command\UpdateTag\UpdateTagCommand;
use App\Tag\Bus\Query\GetTag\GetTagQuery;
use App\Tag\Bus\Query\GetTagList\GetTagListQuery;
use App\Tag\DTO\TagResponse;
use App\Tag\Entity\Tag;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tags')]
#[OA\Tag(name: 'Tags')]
final class TagController extends AbstractApiController
{
    #[Route('', methods: ['GET'])]
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
        description: 'Returns a list of tags',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: TagResponse::class))
        )
    )]
    public function list(
        #[MapQueryParameter]
        int $page = 1,
        #[MapQueryParameter]
        int $limit = 10
    ): JsonResponse {
        $query = new GetTagListQuery(
            page: $page,
            limit: $limit
        );
        $tags = $this->queryBus->dispatch($query);

        return $this->json($tags);
    }

    #[Route('/{id}', methods: ['GET'])]
    #[OA\Response(
        response: 200,
        description: 'Returns a tag',
        content: new OA\JsonContent(ref: new Model(type: TagResponse::class))
    )]
    #[OA\Response(
        response: 404,
        description: 'Tag not found'
    )]
    public function show(int $id): JsonResponse
    {
        $query = new GetTagQuery(id: $id);
        $tag = $this->queryBus->dispatch($query);

        return $this->json($tag);
    }

    #[Route('', methods: ['POST'])]
    #[OA\RequestBody(
        description: 'Tag data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: CreateTagRequest::class))
    )]
    #[OA\Response(
        response: 201,
        description: 'Tag created',
        content: new OA\JsonContent(ref: new Model(type: TagResponse::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    public function create(
        #[MapRequestPayload]
        CreateTagRequest $createRequest
    ): JsonResponse {
        $command = new CreateTagCommand(name: $createRequest->getName());
        /** @var Tag $tag */
        $tag = $this->commandBus->dispatch($command);

        return $this->json(
            TagResponse::fromEntity($tag),
            Response::HTTP_CREATED
        );
    }

    #[Route('/{id}', methods: ['PUT'])]
    #[OA\RequestBody(
        description: 'Tag data',
        required: true,
        content: new OA\JsonContent(ref: new Model(type: UpdateTagRequest::class))
    )]
    #[OA\Response(
        response: 200,
        description: 'Tag updated',
        content: new OA\JsonContent(ref: new Model(type: TagResponse::class))
    )]
    #[OA\Response(
        response: 400,
        description: 'Invalid input'
    )]
    #[OA\Response(
        response: 404,
        description: 'Tag not found'
    )]
    public function update(
        int $id,
        #[MapRequestPayload]
        UpdateTagRequest $updateRequest
    ): JsonResponse {
        $command = new UpdateTagCommand(
            id: $id,
            name: $updateRequest->getName()
        );
        /** @var Tag $tag */
        $tag = $this->commandBus->dispatch($command);

        return $this->json(TagResponse::fromEntity($tag));
    }

    #[Route('/{id}', methods: ['DELETE'])]
    #[OA\Response(
        response: 204,
        description: 'Tag deleted'
    )]
    #[OA\Response(
        response: 404,
        description: 'Tag not found'
    )]
    public function delete(int $id): JsonResponse
    {
        $command = new DeleteTagCommand(id: $id);
        $this->commandBus->dispatch($command);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}

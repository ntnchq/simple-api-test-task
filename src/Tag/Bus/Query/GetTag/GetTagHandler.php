<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTag;

use App\Tag\DTO\TagResponse;
use App\Tag\Repository\TagRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetTagHandler
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {
    }

    public function __invoke(GetTagQuery $query): TagResponse
    {
        $tag = $this->tagRepository->find($query->id);
        if (!$tag) {
            throw new NotFoundHttpException(\sprintf('Tag with ID %d not found', $query->id));
        }

        return TagResponse::fromEntity($tag);
    }
}

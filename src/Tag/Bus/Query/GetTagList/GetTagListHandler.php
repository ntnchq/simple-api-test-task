<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTagList;

use App\Tag\DTO\TagResponse;
use App\Tag\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class GetTagListHandler
{
    public function __construct(
        private readonly TagRepository $tagRepository,
    ) {
    }

    /**
     * @return TagResponse[]
     */
    public function __invoke(GetTagListQuery $query): array
    {
        $tags = $this->tagRepository->findAll();

        $offset = ($query->page - 1) * $query->limit;
        $tags = \array_slice($tags, $offset, $query->limit);

        return array_map(
            fn ($tag) => TagResponse::fromEntity($tag),
            $tags
        );
    }
}

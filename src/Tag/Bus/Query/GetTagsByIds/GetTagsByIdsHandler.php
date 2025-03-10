<?php

declare(strict_types=1);

namespace App\Tag\Bus\Query\GetTagsByIds;

use App\Tag\Entity\Tag;
use App\Tag\Repository\TagRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetTagsByIdsHandler
{
    public function __construct(
        private TagRepository $tagRepository,
    ) {
    }

    /**
     * @return array<Tag>
     */
    public function __invoke(GetTagsByIdsQuery $query): array
    {
        return $this->tagRepository->findByIds($query->ids);
    }
}

<?php

declare(strict_types=1);

namespace App\Article\Repository;

use App\Article\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 *
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Article[] findAll()
 * @method Article[] findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, ?int $limit = null, ?int $offset = null)
 */
final class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @param array<int> $tagIds
     * @param int|null $offset Если null - пагинация не применяется
     * @param int|null $limit Если null - пагинация не применяется
     *
     * @return array<int, Article>
     *
     * @throws \InvalidArgumentException
     */
    public function findByTagsPaginated(array $tagIds, ?int $offset = null, ?int $limit = null): array
    {
        if ($offset !== null && $offset < 0) {
            throw new \InvalidArgumentException('Offset cannot be negative');
        }

        if ($limit !== null && $limit <= 0) {
            throw new \InvalidArgumentException('Limit must be positive');
        }

        $qb = $this->createQueryBuilder('a');

        if ($tagIds !== []) {
            $this->addTagFilter($qb, $tagIds);
        }

        if ($offset !== null && $limit !== null) {
            $qb->setFirstResult($offset)
                ->setMaxResults($limit);
        }

        /** @var array<int, Article> */
        return $qb->getQuery()->getResult();
    }

    /**
     * @param array<int> $tagIds
     */
    private function addTagFilter(QueryBuilder $qb, array $tagIds): void
    {
        $tagCount = \count($tagIds);

        $qb->join('a.articleTags', 'at')
            ->join('at.tag', 't')
            ->andWhere('t.id IN (:tagIds)')
            ->setParameter('tagIds', $tagIds)
            ->groupBy('a.id')
            ->having('COUNT(DISTINCT t.id) = :tagCount')
            ->setParameter('tagCount', $tagCount);
    }
}

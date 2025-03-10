<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\Article\Entity\Article;
use App\Tag\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture implements DependentFixtureInterface
{
    public const TEST_ARTICLE_1 = 'Test Article 1';
    public const TEST_ARTICLE_2 = 'Test Article 2';

    public function load(ObjectManager $manager): void
    {
        $article1 = new Article(self::TEST_ARTICLE_1);
        $article1->addTag($this->getReference('tag-1', Tag::class));
        $manager->persist($article1);

        $article2 = new Article(self::TEST_ARTICLE_2);
        $article2->addTag($this->getReference('tag-1', Tag::class));
        $article2->addTag($this->getReference('tag-2', Tag::class));
        $manager->persist($article2);

        $manager->flush();

        $this->addReference('article-1', $article1);
        $this->addReference('article-2', $article2);
    }

    public function getDependencies(): array
    {
        return [
            TagFixtures::class,
        ];
    }
}

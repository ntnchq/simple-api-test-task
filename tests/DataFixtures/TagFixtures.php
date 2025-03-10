<?php

declare(strict_types=1);

namespace App\Tests\DataFixtures;

use App\Tag\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixtures extends Fixture
{
    public const TEST_TAG_1 = 'Test Tag 1';
    public const TEST_TAG_2 = 'Test Tag 2';

    public function load(ObjectManager $manager): void
    {
        $tag1 = new Tag(self::TEST_TAG_1);
        $manager->persist($tag1);

        $tag2 = new Tag(self::TEST_TAG_2);
        $manager->persist($tag2);

        $manager->flush();

        $this->addReference('tag-1', $tag1);
        $this->addReference('tag-2', $tag2);
    }
}

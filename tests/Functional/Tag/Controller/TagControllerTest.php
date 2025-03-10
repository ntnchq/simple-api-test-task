<?php

declare(strict_types=1);

namespace App\Tests\Functional\Tag\Controller;

use App\Tag\Entity\Tag;
use App\Tests\DataFixtures\TagFixtures;
use App\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Response;
use App\Article\Entity\Article;

class TagControllerTest extends FunctionalTestCase
{
    public function testGetTags(): void
    {
        $this->client->request('GET', '/api/tags');

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(2, $responseData);

        $tagNames = array_map(fn ($tag) => $tag['name'], $responseData);

        $this->assertContains(TagFixtures::TEST_TAG_1, $tagNames);
        $this->assertContains(TagFixtures::TEST_TAG_2, $tagNames);
    }

    public function testGetTag(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(['name' => TagFixtures::TEST_TAG_1]);

        $this->client->request('GET', '/api/tags/'.$tag->getId());

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals($tag->getId(), $responseData['id']);
        $this->assertEquals(TagFixtures::TEST_TAG_1, $responseData['name']);
    }

    public function testCreateTag(): void
    {
        $tagData = [
            'name' => 'New Test Tag',
        ];

        $this->client->request(
            'POST',
            '/api/tags',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($tagData)
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals('New Test Tag', $responseData['name']);

        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(['name' => 'New Test Tag']);
        $this->assertNotNull($tag);
    }

    public function testUpdateTag(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(['name' => TagFixtures::TEST_TAG_1]);

        $tagData = [
            'name' => 'Updated Test Tag',
        ];

        $this->client->request(
            'PUT',
            '/api/tags/'.$tag->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($tagData)
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertEquals($tag->getId(), $responseData['id']);
        $this->assertEquals('Updated Test Tag', $responseData['name']);

        $this->entityManager->refresh($tag);
        $this->assertEquals('Updated Test Tag', $tag->getName());
    }

    public function testDeleteTag(): void
    {
        $tagRepository = $this->entityManager->getRepository(Tag::class);
        $tag = $tagRepository->findOneBy(['name' => TagFixtures::TEST_TAG_2]);
        $tagId = $tag->getId();

        $articleRepository = $this->entityManager->getRepository(Article::class);
        $articles = $articleRepository->findAll();
        foreach ($articles as $article) {
            $article->removeTag($tag);
        }
        $this->entityManager->flush();

        $this->client->request('DELETE', '/api/tags/'.$tagId);

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->entityManager->clear();
        $deletedTag = $tagRepository->find($tagId);
        $this->assertNull($deletedTag);
    }
}
